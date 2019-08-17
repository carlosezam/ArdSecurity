#include <EEPROM.h>
#include <Ethernet.h>
#include <SoftwareSerial.h>

#define DEBUG
//#define DEBUG_HTTP

#ifdef DEBUG
#define DEBUG_PRINT(x)  Serial.print(x)
#define DEBUG_PRINTLN(x)  Serial.println(x)
#define DEBUG_LOG(t,m)  Serial.print(t); Serial.println(m)
#else
#define DEBUG_PRINT(x)
#define DEBUG_PRINTLN(x)
#define DEBUG_LOG(t,m)
#endif

#ifdef DEBUG_HTTP
#define DEBUG_HTTP(x)  Serial.print(x)
#else
#define DEBUG_HTTP(x)
#endif

#define GET_GLOBAL_CONFIG( gc ) (EEPROM.get( 0, gc))
#define PUT_GLOBAL_CONFIG( gc ) (EEPROM.put( 0, gc))

#define SENSOR_MAGNETICO  1
#define SENSOR_PRESENCIA  2
#define SENSOR_BAR_INFRA  3

#define PIN_ALARMA 2
#define PIN_ACTIVO 3
#define PIN_ETHERNET_R 7
#define PIN_ETHERNET_G 6
#define PIN_ETHERNET_B 5

#define PIN_SIM_RX 19
#define PIN_SIM_TX 18

#define NUM_LIMIT_SENSORES 6



/*
 * Definición de estructuras
 */

struct GlobalConfig
{
  struct IdentityConfig{
    char correo[50];
    char nombre[20];
  } id;
  struct ShellConfig
  {
    byte mac[6];
    byte ip[4];
  } shell;
  struct SystemConfig
  {
    int num_sensores;
    bool alarma;
    bool activo;
  } sys;
  struct ApiConfig
  {
    int  port;
    char addr[40];
    char path_push[40];
    char path_sync[40];
  } api;
  struct SimConfig{
    char num_1[15];
    char num_2[15];
  } sim;
  
} global;

struct SensorConfig{
  SensorConfig(){}
  SensorConfig(int t, int r, int i, int o, bool a):tipo(t), ranura(r), pin_inp(i), pin_out(o), activo(a) {} 
  int tipo;
  int ranura;
  int pin_inp;
  int pin_out;
  bool activo;
} sensor[ NUM_LIMIT_SENSORES];

struct Command{
  const char *label;
  void (*function)(char **, int);
};



/*
 * Definición de clases manejadoras de los sensores
 */
 
class SensorManager
{
  public:
  bool alarma;
  SensorConfig sc;
  
  SensorManager( SensorConfig &_sc ) : sc(_sc) ,alarma(false) {} 
  
  virtual bool loop() = 0;
};

typedef SensorManager *ptrSensorManager;


class SensorPir : public SensorManager
{
public:
  
  SensorPir( SensorConfig &sc ) : SensorManager( sc ) {
    pinMode( sc.pin_inp, INPUT );
  }

  bool loop()
  {
    DEBUG_PRINT("Sensor pir: ");
    DEBUG_PRINT( sc.ranura );
    DEBUG_PRINT( " -> ");
    DEBUG_PRINTLN( digitalRead(sc.pin_inp) );
    
    if( !alarma )
    {
      if( digitalRead( sc.pin_inp ) )
      {
        alarma = true;
        return true;
      }
    }
    return false;
  }
  
};



class SensorMagnetico : public SensorManager
{
public:

  SensorMagnetico( SensorConfig &sc ) : SensorManager( sc ) {}

  bool loop()
  {
    DEBUG_PRINT("Sensor magnetico: ");
    DEBUG_PRINT( sc.ranura );
    DEBUG_PRINT( " -> ");
    DEBUG_PRINTLN( analogRead(sc.pin_inp) );
    if( !alarma ) {
      if( analogRead(sc.pin_inp) < 1000 ) {
         alarma = true;
         return true;
      }
    }

    return false;
  }
};



class SensorIR : public SensorManager
{
  public:
  
  SensorIR( SensorConfig &sc ) : SensorManager( sc ) {}

  bool loop()
  {
    DEBUG_PRINT("Sensor ir: ");
    DEBUG_PRINT( sc.ranura );
    DEBUG_PRINT( " -> ");
    DEBUG_PRINTLN( analogRead(sc.pin_inp) );
    if( !alarma ) {
      if( analogRead(sc.pin_inp) < 180 ) {
         alarma = true;
         return true;
      }
    }

    return false;
  }
};

bool send_report( int );
ptrSensorManager *load_sensors( SensorConfig sensor_config[] );
SensorManager *get_sensor_manager( int  );

SensorManager **s;
EthernetClient client;
//SoftwareSerial sim800l( PIN_SIM_RX, PIN_SIM_TX );
unsigned long last_sync = 0;

int strsplit( char **target, const char *str, const char *delimiter, int max_tokens );

void setup() {
  pinMode( PIN_ALARMA, OUTPUT );
  pinMode( PIN_ACTIVO, OUTPUT );
  pinMode( PIN_ETHERNET_R, OUTPUT );
  pinMode( PIN_ETHERNET_G, OUTPUT );
  pinMode( PIN_ETHERNET_B, OUTPUT );
  
  Serial.begin(9600);
  Serial2.begin(9600);
  
  get_initial_global_config( global );
  get_initial_sensor_config( sensor );

  digitalWrite( PIN_ACTIVO, HIGH );
  Serial2.println("AT+CMGF=1"); delay(1000);
  digitalWrite( PIN_ACTIVO, LOW );
  Serial2.println("AT+CNMI=1,2,0,0,0"); delay(1000);
  digitalWrite( PIN_ACTIVO, HIGH );
  Serial2.println(); 
  
  Ethernet.begin( global.shell.mac, global.shell.ip );
  delay(5000);
  
  print_global_config( global );
  print_sensor_config( sensor );

  s = load_sensors( sensor );
  Serial.print("Sensores activos: ");
  Serial.println( global.sys.num_sensores );

  sim_sms(global.sim.num_1, "ArdSecurity Activado");
}

String string_temp;
const int interval_sync = 5000;

void loop() {
  //DEBUG_PRINTLN("[.] loop()");

  if( read_serial(string_temp) )
  {
    DEBUG_LOG("read_serial(string_temp) -> ", string_temp);
    command_execute( string_temp.c_str() );
  }

  if( read_serial2(string_temp) )
  {
    DEBUG_LOG("sim800l:\n", string_temp);
    //command_execute( string_temp.c_str() );

    char *num = NULL;
    int index;
    
    if(string_temp.indexOf(global.sim.num_1)>=0 ) num = global.sim.num_1;
    if(string_temp.indexOf(global.sim.num_2)>=0 ) num = global.sim.num_2;

    index = string_temp.indexOf("+CMT:");
    
    if(num != NULL && index >=0 ) {
    
    string_temp = string_temp.substring( index + 1);
    
    index = string_temp.indexOf("\n");
    string_temp = string_temp.substring(  index > 0 ? (index+1) : 0  );
    DEBUG_LOG("read_sim800l(string_temp) -> ", string_temp);
    
    for( int i = 0; i < string_temp.length(); ++i )
    {
      string_temp[ i ] = tolower( string_temp[i] );
    }

    if(string_temp.indexOf("reporte")>=0){
      char reporte[100];
      sprintf(reporte, "Sistema activo: %s\nAlarma Intrusos: %s\nNumero de sensores activos: %d", (global.sys.activo?"si":"no"), (global.sys.alarma?"si":"no"),  global.sys.num_sensores );
      //command_execute("config sys activo=true");
      sim_sms(num, reporte);
    } else if(string_temp.indexOf("desactivar")>=0){
      command_execute("config sys activo=false");
      sim_sms(num, "Sistema Desactivado");
    } else if(string_temp.indexOf("activar")>=0){
      command_execute("config sys activo=true");
      sim_sms(num, "Sistema Activado");
    } else if(string_temp.indexOf("silencio")>=0){
      command_execute("config sys alarma=false");
      sim_sms(num, "Sirena silenciada");
    } else {
      string_temp.trim();
      if( command_execute(string_temp.c_str()) )
      {
        sim_sms(num, "OK");
      } else {
        sim_sms(num, "Comando no reconocido");
      }
    }
    }
  }
  if(  ( millis()-last_sync ) > interval_sync  )
  {
    last_sync = millis();
    if( get_sync( string_temp ) )
    {
      command_execute( string_temp.c_str() );  
    }
  }

  read_next_sensor();

  digitalWrite(PIN_ALARMA, global.sys.alarma);
  digitalWrite(PIN_ACTIVO, global.sys.activo);
  
  delay(1000);
}

void read_next_sensor()
{
  static int index = 0;
  ptrSensorManager sm;
  
  if( !global.sys.activo ) return;
  
  sm = s[ index ];
  DEBUG_LOG("read_next_sensor() -> ", index);
  if( sm != NULL && sm->sc.activo )
  {
    
    DEBUG_PRINT("\n\n[.] verificando sensor en ranura ");
    DEBUG_PRINTLN( sm->sc.ranura );
    if( sm->loop() )
    {
      Serial.println("[!] Sensor Activo, alarma activada");
      send_sim_report();
      send_report( sm->sc.ranura );
      global.sys.alarma = true;
    } else {
      DEBUG_PRINTLN("[.] Sensor normal");
    }
    
  }
  
  ++index %= NUM_LIMIT_SENSORES;
  
}
int strsplit( char **target, const char *str, const char *delimiter, int max_tokens )
{
  int   t   = 0;
  char *tok = NULL;
  
  tok = strtok( str, delimiter );
  if( tok != NULL )
  {
    do{
      //Serial.print("token: ");Serial.println(tok);
      target[t++] = tok;
    } while( ( t < max_tokens ) && (tok = strtok(NULL, delimiter)) != NULL  );
  }
 
  return t;
}



bool send_report( int ranura )
{
  
  char strbuffer[150];
  String temp;
  sprintf( strbuffer, "correo=%s&nombre=%s&ranura=%d", global.id.correo, global.id.nombre, ranura);
  Serial.print("[+] Enviando reporte: ");
  Serial.println( strbuffer );
  if ( api_request( global.api.path_push, strbuffer) )
  {
    api_response( &temp );
    Serial.print( temp );    
    return true;
  }

  return false;
}


bool read_serial2( String &string )
{
  string = "";
  if( Serial2.available() )
  {
    do
    {
      string.concat( (char)Serial2.read() );
      delay(20);
    }while( Serial2.available() );
  }

  return string.length() > 0;
}

bool get_sync( String &string )
{
  char strbuffer[150];
  sprintf( strbuffer, "correo=%s&nombre=%s", global.id.correo, global.id.nombre);
  int code;
  if ( api_request( global.api.path_sync, strbuffer) )
  {
    code = api_response( &string );
    DEBUG_LOG("[+] sync success: ", string);
    command_execute( string.c_str() );
    return true;
  } 

  DEBUG_LOG("[!] sync failed: ", code );
  return false;
}





void send_sim_report()
{
  Serial.println("[+] Enviando alerta a los numeros celulares");
  DEBUG_LOG("Enviando mensaje a: ", global.sim.num_1);
  sim_sms(global.sim.num_1, "Alerta de intruso");

  DEBUG_LOG("Enviando mensaje a: ", global.sim.num_2);
  sim_sms(global.sim.num_2, "Alerta de intruso");

  DEBUG_LOG("Haciendo llamada a: ", global.sim.num_1);
  sim_call(global.sim.num_1, 18 * 1000);
}

void sim_call( const char *num, const int timeout )
{
  char sbuffer[50];  
  sprintf( sbuffer, "ATD%s;",num );
  
  Serial2.println(sbuffer); delay(timeout);
  Serial2.println("ATH"); delay(1000);
  Serial2.flush();
}
void sim_sms( const char *num, const char *msg)
{
  char sbuffer[50];  
  sprintf( sbuffer, "AT+CMGS=\"%s\"",num );
  
  Serial2.println(sbuffer); delay(100);
  Serial2.print(msg); delay(100);
  Serial2.print(char(26)); delay(100);
  Serial2.println(); delay(3000);
  Serial2.flush();
}



ptrSensorManager *load_sensors( SensorConfig sensor_config[] )
{
  int count = 0;
  
  if( s != NULL ) delete[] s;
  
  ptrSensorManager *s = new ptrSensorManager[ NUM_LIMIT_SENSORES ];
  
   
  for( int i = 0; i < NUM_LIMIT_SENSORES; ++i )
  {
    if( sensor_config[i].activo ) count++;
    s[ i ] = get_sensor_manager( sensor_config[ i ] );
  }

  global.sys.num_sensores = count;

  return s;
}


SensorManager *get_sensor_manager( SensorConfig &sc )
{
  switch( sc.tipo )
  {
    case SENSOR_MAGNETICO: return new SensorMagnetico( sc );
    case SENSOR_PRESENCIA: return new SensorPir( sc );
    case SENSOR_BAR_INFRA: return new SensorIR( sc );
  }
  
  return NULL;
}






/*
 * Serial Command
 **/

bool read_serial( String &string )
{
  string = "";
  if( Serial.available() )
  {
    do
    {
      string.concat( (char)Serial.read() );
      delay(20);
    }while( Serial.available() );
  }

  return string.length() > 0;
}


void cmd_load_global( char **, int );
void cmd_load_sensor( char **, int );

void cmd_save_global( char **, int );
void cmd_save_sensor( char **, int );

void cmd_print_global( char **, int );
void cmd_print_sensor( char **, int );

void cmd_set_config( char **, int );
void cmd_set_sensor( char **, int );

void cmd_api_push( char **, int);
void cmd_api_sync( char **, int);

bool command_execute( const char *const _strcmd )
{
  Serial.print("comand_execute: ");
  Serial.println(_strcmd);

  char *strcmd = new char[strlen(_strcmd) + 1];
  strcpy( strcmd, _strcmd);
  
  static Command commands[] ={
    {"config-print", cmd_print_global},
    {"sensor-print", cmd_print_sensor},
    
    {"config-load",  cmd_load_global },
    {"sensor-load",  cmd_load_sensor },
    
    {"config-save",  cmd_save_global },
    {"sensor-save",  cmd_save_sensor },
    
    {"config",   cmd_set_config  },
    {"sensor",   cmd_set_sensor  },
    
    {"api-push", cmd_api_push },
    {"api-sync", cmd_api_sync },
  };
  char *argv[5] = {0};
  int argc = strsplit( argv, strcmd, " ", 5);

  if( argc < 1 ) return;

  bool error = true;
  
  for( auto cmd : commands )
  {
    if( strcmp( cmd.label, argv[0]) == 0 ){
      error = false;
      cmd.function( &argv[1], argc - 1 );    
    }
  }

  if( error )
  {
    Serial.println("[!] Comando invalido");
  }
  delete[] strcmd;
  return !error;
}


void cmd_api_push( char **argv, int argc )
{
  
}

void cmd_api_sync( char **argv, int argc )
{
  
}

void cmd_load_sensor( char **, int )
{
  get_sensor_config( sensor );  
}

void cmd_save_sensor( char **, int )
{
  put_sensor_config( sensor );  
}

struct CommandSet {
  char *label;
  void (*function)( const char*, const char* );
};

void cmd_set_identity( const char*, const char *);
void cmd_set_shell( const char*, const char *);
void cmd_set_system( const char*, const char *);
void cmd_set_api( const char*, const char *);
void cmd_set_sim( const char*, const char *);

void cmd_set_sensor( char **argv, int argc )
{
  if( argc < 2 ) 
  {
    Serial.println( "uso sensor {on|off} {ranura}" );
    return;
  }

  int ranura = atoi( argv[1] );
  if( ranura > 0 && ranura <= NUM_LIMIT_SENSORES )
  {
    if( strcmp(argv[0],"on") == 0){
      Serial.println("Sensor activado.");
      sensor[ ranura - 1 ].activo = true;
    }
    else if( strcmp(argv[0],"off") == 0){
      Serial.println("Sensor desactivado.");
      sensor[ ranura - 1 ].activo = false;
    }
    else
    {
      Serial.println("Datos no validos.");
    }
    
  }
  
}

void cmd_set_config( char **argv, int argc )
{ 
  static CommandSet seters[] = {
    {"id", cmd_set_identity },
    {"shell", cmd_set_shell },
    {"sys", cmd_set_system },
    {"api", cmd_set_api },
    {"sim", cmd_set_sim }
  };

  if( argc < 2 ) {
    Serial.println( "uso config {id|shell|system|api} {attr=value}" );
    return;
  }  

  bool valid = false;
  char *v[2];
  int   s = strsplit( v, argv[1], "=", 2);
  
  for( auto cmd : seters )
  {
    if( strcmp( cmd.label, argv[0]) == 0 )
    {
      if( s == 2 )
      {
        valid = true;
        cmd.function( v[0], v[1] );
      }        
    }
  }

  if(!valid)
  {
    Serial.println( "uso set {id|shell|sys|api|sim} {attr=value}" );
  }
}

void cmd_set_identity( const char* attr, const char *value )
{
  if( strcmp(attr, "correo") == 0 ){
    strcpy( global.id.correo, value );
    Serial.println("correo establecido");
  } else if( strcmp(attr, "nombre") == 0){
    strcpy( global.id.nombre, value);
    Serial.println("nombre establecido");
  } else {
    Serial.println( "Error: atributo desconocido" );
  }
  
}

void cmd_set_sim( const char* attr, const char *value )
{
  if( strcmp(attr, "num1") == 0 )
  {
    strcpy( global.sim.num_1, value );
    Serial.println("numero 1 establecido");
  }
  else if( strcmp(attr, "num2") == 0)
  {
    strcpy( global.sim.num_2, value );
    Serial.println("numero 2 establecido");
  }
  else
  {
    Serial.println( "Error: atributo desconocido" );
  }
  
}

void cmd_set_shell( const char* attr, const char *value )
{
  char *tempv[6];
  int tempc;
  if( strcmp(attr, "mac") == 0)
  {
    tempc = strsplit( tempv, value, ":", 6 );
  
    if(tempc == 6)
    {
      for( int i = 0; i < 6; i++ )
      {
        sscanf( tempv[i], "%x", &tempc );
        global.shell.mac[i] = tempc % 256;
      }
    } else {
      Serial.println("MAC invalida");
    }
    
  }
  else if( strcmp( attr, "ip") == 0 )
  {
    tempc = strsplit( tempv, value, ".", 4);
    if( tempc == 4 )
    {
      for( int i = 0; i < 4; ++i )
      {
        sscanf( tempv[i], "%d", &tempc );
        global.shell.ip[i] = tempc;
      }
    } else {
      Serial.println("IP invalida");
    }
  } else {
    Serial.println("Error: Atributo desconocido");
  }
}

void cmd_set_system( const char* attr, const char *value )
{
  Serial.print("attr: ");
  Serial.print( attr );
  Serial.print(", value: ");
  Serial.println( value );
  if( strcmp(attr, "alarma") == 0 ){
    if( strcmp(value, "true") == 0 )
      global.sys.alarma = true;
    else if ( strcmp(value, "false") == 0)
      disable_alarma();
    else
      Serial.println("Error: valor invalido");
  }
  else if( strcmp(attr, "activo") == 0 ){
    if( strcmp(value, "true") == 0 )
      global.sys.activo = true;
    else if ( strcmp(value, "false") == 0)
      global.sys.activo = false;
    else
      Serial.println("Error: valor invalido");
  } else {
    Serial.println("Error: atributo desconocido");
  }
}

void disable_alarma()
{
  global.sys.activo = false;
  global.sys.alarma = false;


  for( int i = 0; i < NUM_LIMIT_SENSORES; ++i )
  {
    if( s[ i ] != NULL )
    {
      s[ i ]->alarma = false;
      Serial.print("Sensor ");
      Serial.print(i);
      Serial.println(" restablecido");
    } else {
      Serial.println("Sensor null");
    }
  }
}

void cmd_set_api( const char* attr, const char *value )
{
  if( strcmp( attr, "server") == 0 )
  {
    strcpy( global.api.addr, value );
  } else if( strcmp( attr, "port") == 0 )
  {
    global.api.port = atoi( value );
  } else if( strcmp( attr, "push") == 0 )
  {
    strcpy( global.api.path_push, value );
  } else if( strcmp( attr, "sync") == 0 )
  {
    strcpy( global.api.path_sync, value );
  } else {
    Serial.println( "Error: atributo desconocido" );
  }
}

void cmd_save_global( char **, int )
{
  PUT_GLOBAL_CONFIG( global );
  Serial.println("[+] Configuración global guardada");
  //save_global_config( global );
}
void cmd_load_global( char **, int )
{
  GET_GLOBAL_CONFIG( global );
  Serial.println("[+] Configuración global cargada");
  //load_global_config( global );
}

void cmd_print_global( char **, int )
{
  print_global_config( global );
}

void cmd_print_sensor( char **, int )
{
  print_sensor_config( sensor );
}









void get_initial_global_config( GlobalConfig &global )
{
  
  GlobalConfig initial_config
  {
    // IdentityConfig => id
    {
      "2d.roblero@gmail.com",  //correo
      "ArdSecurity",                //nombre
    },

    // ShellConfig => shell
    { 
      {0xDE, 0xAD, 0xBE, 0xEF, 0xFE, 0xED}, // MAC Address
      {192,168,1,201} // IP Address
    },

    // SystemConfig => system
    { 
      0, // num sensores
      false, // alarma (eg sirena)
      true  // monitoreo activo
    },

    // ApiConfig => api
    { 
      80,
      "192.168.1.10",
      "/ArdSecurity/index.php/alarma/notify",
      "/ArdSecurity/index.php/service/sync"
    },
    {
      "9621397524",
      "+529621111111"
    }
  };

  global = initial_config;
}

void get_initial_sensor_config( SensorConfig sensor_config[] ){
  
  sensor_config[0] = SensorConfig( SENSOR_MAGNETICO, 1, A0, 0, true);
  sensor_config[1] = SensorConfig( SENSOR_MAGNETICO, 2, A1, 0, false);
  
  sensor_config[2] = SensorConfig( SENSOR_PRESENCIA, 6, 22, 0, false);
  sensor_config[3] = SensorConfig( SENSOR_PRESENCIA, 7, 23, 0, false);
  
  sensor_config[4] = SensorConfig( SENSOR_BAR_INFRA, 11, A8, 0, false);
  sensor_config[5] = SensorConfig( SENSOR_BAR_INFRA, 12, A9, 0, false);

  return sensor_config;
}

void get_sensor_config( SensorConfig sensor_config[] ){
  for( int i = 0, addr = 512; i < NUM_LIMIT_SENSORES; ++i, addr += sizeof(SensorConfig)  )
  {
    EEPROM.get( addr, sensor_config[i] );
  }
  Serial.println("Configuracion de los sensores cargada desde la EEPROM");
}

void put_sensor_config( SensorConfig sensor_config[] ){
  for( int i = 0, addr = 512; i < NUM_LIMIT_SENSORES; ++i, addr += sizeof(SensorConfig)  )
  {
    EEPROM.put( addr, sensor_config[i] );
  }
  Serial.println("Configuraciom de los sensores guardada en la EEPROM");
}

/*
 * Imprime la configuración global
 * 
 * @param 
 */
void print_global_config( GlobalConfig &global )
{
  char strbuffer[200];
  Serial.println("Configuración global");
  
  sprintf(strbuffer, "Identity:\n  correo => %s\n  nombre => %s\n", global.id.correo, global.id.nombre);
  Serial.print(strbuffer);
  
  sprintf(strbuffer, "Shell:\n  mac => %0X:%0X:%0X:%0X:%0X:%0X\n  ip => %d.%d.%d.%d\n",
    global.shell.mac[0], global.shell.mac[1], global.shell.mac[2], global.shell.mac[3], global.shell.mac[4], global.shell.mac[5],
    global.shell.ip[0], global.shell.ip[1], global.shell.ip[2], global.shell.ip[3]);
  Serial.print(strbuffer);

  sprintf(strbuffer, "System:\n  num sensores => %d\n  alarma => %s\n  activo => %s\n",
    global.sys.num_sensores, global.sys.alarma ? "true" : "false", global.sys.activo ? "true" : "false");
  Serial.print(strbuffer);

  sprintf(strbuffer, "Api:\n  server => %s\n  port => %d\n  push => %s\n  sync => %s\n",
    global.api.addr, global.api.port, global.api.path_push, global.api.path_sync);
  Serial.print(strbuffer);

  sprintf(strbuffer, "Sim:\n  num 1 => %s\n  num 2 => %s\n",
    global.sim.num_1, global.sim.num_2);
  Serial.print(strbuffer);
  Serial.println();
}


/*
 * Imprime la configuración de los sensores 
 * 
 * @param SensorConfig[] sensor_config arreglo con los datos de configuración
 */
 
void print_sensor_config( SensorConfig sensor_config[] ){
  char sbuffer[100];
  Serial.println("Configuración de los sensores");
  for( int i = 0; i < NUM_LIMIT_SENSORES; ++i )
  {
    sprintf( sbuffer, "Ranura: %d, Tipo: %d, Input: %d, Output: %d, Activo: %s\n",
      sensor_config[i].ranura, sensor_config[i].tipo, sensor_config[i].pin_inp, sensor_config[i].pin_out, sensor_config[i].activo ? "true" : "false" );
    Serial.print( sbuffer );
  }
  Serial.println();
}








/*
 * Realiza una petición al servidor.
 * Encodea la ruta, los datos y demás en el cuerpo de la petición.
 * 
 * @param char* path ruta uri de la petición
 * @param char* body cuerpo de la petición
 * @return bool devuelve true si la conexión se realizó correctamente
 */
 
bool api_request( const char *path, const char *body ) {
  // close any connection before send a new request.
  // This will free the socket on the WiFi shield
  client.stop();

  DEBUG_HTTP("[.] conectando a: ");
  DEBUG_HTTP(global.api.addr);
  DEBUG_HTTP("... ");
  
  client.setTimeout(300);

  digitalWrite( PIN_ACTIVO, LOW);
  digitalWrite( PIN_ETHERNET_R, HIGH);
  digitalWrite( PIN_ETHERNET_G, HIGH);
  digitalWrite( PIN_ETHERNET_B, HIGH);
  // if there's a successful connection:
  if (client.connect(global.api.addr, global.api.port)) {

  digitalWrite( PIN_ACTIVO, LOW);
  digitalWrite( PIN_ETHERNET_R, LOW);
  digitalWrite( PIN_ETHERNET_G, LOW);
  digitalWrite( PIN_ETHERNET_B, HIGH);
    Serial.println(" conectado!");
    //client.println("POST / HTTP/1.1");
    client.print("POST ");
    client.print(path);
    client.print(" HTTP/1.1\r\n");
    
    //client.println("Host: www.arduino.cc");
    client.print("Host: ");
    client.print(global.api.addr);
    client.print("\r\n");
    
    client.print("User-Agent: arduino-ethernet\r\n");
    client.print("Connection: close\r\n");

    if( body != NULL )
    {
      char contentLength[25];
      sprintf(contentLength, "Content-Length: %d\r\n", strlen(body));
      client.print(contentLength);
      client.print("Content-Type: application/x-www-form-urlencoded\r\n");
    }
    
    client.print("\r\n");

    if(body != NULL){
      client.print(body);
      client.print("\r\n");
      client.print("\r\n");
    }

    //lastConnectionTime = millis();
    digitalWrite( PIN_ACTIVO, HIGH);
    digitalWrite( PIN_ETHERNET_R, LOW);
    digitalWrite( PIN_ETHERNET_G, HIGH);
    digitalWrite( PIN_ETHERNET_B, LOW);
    DEBUG_HTTP(" success");
    return true;
  } else {
    digitalWrite( PIN_ACTIVO, HIGH);
    digitalWrite( PIN_ETHERNET_R, HIGH);
    digitalWrite( PIN_ETHERNET_G, LOW);
    digitalWrite( PIN_ETHERNET_B, LOW);
    // if you couldn't make a connection:
    DEBUG_HTTP(" error");
    return false;
  }

}



/**
 * Lee la respuesta del servidor, guarda el cuerpo de la respuesta en un String
 * y devuelve el código de respuesta del servidor
 * 
 * @param String* _response puntero al String donde se almacenarán los datos de la respuesta
 * @return int codigo de respuesta del servidor
 */
 
int api_response(String *_response )
{
  // an http request ends with a blank line
  boolean currentLineIsBlank = true;
  boolean httpBody = false;
  boolean inStatus = false;

  char statusCode[4];
  int i = 0;
  int code = 0;
  (*_response) = "";

  client.setTimeout(3000);  
  while (client.connected()) {

    if (client.available()) {

      char c = client.read();
      
      if(c == ' ' && !inStatus){
        inStatus = true;
      }

      if(inStatus && i < 3 && c != ' '){
        statusCode[i] = c;
        i++;
      }
      if(i == 3){
        statusCode[i] = '\0';
        code = atoi(statusCode);
      }

      if(httpBody){
        //only write _response if its not null
        if(_response != NULL) _response->concat(c);
      }
      else
      {
          if (c == '\n' && currentLineIsBlank) {
            httpBody = true;
          }

          if (c == '\n') {
            // you're starting a new line
            currentLineIsBlank = true;
          }
          else if (c != '\r') {
            // you've gotten a character on the current line
            currentLineIsBlank = false;
          }
      }
    } // Client available
  } // Client connected
  
  return code;
}
