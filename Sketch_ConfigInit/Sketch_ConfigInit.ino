#include <EEPROM.h>

#define LIMIT_CONFIG_S 20
#define OFFSET_CONFIG_S 100
#define ADDRES_CONFIG_S( index ) (OFFSET_CONFIG_S+( index * sizeof(SensorConfig)))

struct DeviceConfig{ char email[50]; char alias[20]; };

struct SensorConfig { char alias[6]; byte pines[3]; byte typsen; byte estado; };

const SensorConfig EMPTY_CONFIG = {"NULL", {0, 0, 0}, 0, 0};
const DeviceConfig INITIAL_CONFIG = {"NULL","NULL"};


void setup() {
  Serial.begin(9600);
  while (!Serial){
    ;
  }

}

void loop() {
  
  String temp;
  if ( Serial.available() )
  {
    while ( Serial.available() )
    {
      temp.concat( (char)Serial.read() );
      delay(25);
    }
   
    hs_cli(temp);

  }
}

//UTILS

void get_config( int index, SensorConfig &c ) {
  EEPROM.get( ADDRES_CONFIG_S(index), c );
}

void set_config( int index, const SensorConfig &c ){
  SensorConfig t;
  get_config( index, t );
  if ( strcmp(c.alias, t.alias) == 0 && c.estado == t.estado && t.pines[0] == c.pines[0] && t.pines[1] == c.pines[1] && t.pines[2] == c.pines[2] )
      return;
  EEPROM.put( ADDRES_CONFIG_S(index), c );
}

bool configcmp( const SensorConfig &c, const SensorConfig &t )
{
  if ( strcmp(c.alias, t.alias) == 0 && c.estado == t.estado && t.pines[0] == c.pines[0] && t.pines[1] == c.pines[1] && t.pines[2] == c.pines[2] )
    return true;
  else
    return false;
}

String config_toString( const SensorConfig &c ) {
  char tmp[20];
  sprintf( tmp, "%s:%d:%d,%d,%d:%d", c.alias, c.typsen, c.pines[0], c.pines[1], c.pines[2], c.estado );
  return String(tmp);
}
void strncpyln( char *target, char *source, int limit )
{
  int len = strlen(source);
  int lim = len < limit ? len : limit - 1; 
  strncpy( target, source, lim );
  target[ lim ] = '\0';
}


// CLICONFIG
void hs_cli_on( char **argv, int argc )
{
  int ranura;
  SensorConfig sct;
  
  if( argc == 0){
    Serial.println("modo de uso: ON [ranura]");
  } else {
    ranura = atoi( argv[0] );
    get_config(ranura, sct);
    
    if( ! configcmp( sct, EMPTY_CONFIG ) )
    {
      sct.estado = 1;
      set_config(ranura, sct );  
    }
  }
}

void hs_cli_off( char **argv, int argc )
{
  int ranura;
  SensorConfig sct;
  
  if( argc == 0){
    Serial.println("modo de uso: OFF [ranura]");
  } else {
    ranura = atoi( argv[0] );
    get_config(ranura, sct);
    
    if( ! configcmp( sct, EMPTY_CONFIG ) )
    {
      sct.estado = 0;
      set_config(ranura, sct );  
    }
  }
}

void hs_cli_del( char **argv, int argc )
{
  if( argc == 0){
    Serial.println("modo de uso: DEL [ranura]");
  } else {
    int ranura = atoi( argv[0] );
    set_config(ranura, EMPTY_CONFIG );
  }
}

void hs_cli_get( char **argv, int argc )
{
  SensorConfig sct;
  char temp[3];
  Serial.println(argc);
  if ( argc == 0 ) {
    for( int i = 0; i < LIMIT_CONFIG_S; ++i )
    {
      get_config( i, sct );
      sprintf(temp, "%02d", i);
      
      Serial.print(temp);
      Serial.print("->");
      Serial.print( config_toString(sct) );
      Serial.print( (i+1) % 5  == 0 ? '\n' : '\t' );
    }
  } else {
    int ranura = atoi( argv[0] );
    get_config( ranura, sct );
    Serial.println( config_toString(sct) );
  }
}

void hs_cli_set( char **argv, int argc )
{
  if( argc < 4 ){
    Serial.println("modo de uso: set ranura:alias:tipo:pines:estado");
    return;
  }
  
  SensorConfig sct;
  int ranura;
  
  ranura = atoi( argv[0] ); //obtiene el numero de ranura
  ranura %= LIMIT_CONFIG_S; //realiza un trim numerico
  
  //obtiene el alias
  strncpyln( sct.alias, argv[1], 6 );

  //obtiene el tipo
  sct.typsen = atoi( argv[2] );

  //obtiene los pines
  sct.pines[0] = atoi( strtok( argv[3], "," ) );
  sct.pines[1] = atoi( strtok( NULL, "," ) );
  sct.pines[2] = atoi( strtok( NULL, "," ) );

  //obtiene el estado (on/off)
  sct.estado = strcmp( argv[4], "ON") == 0 ? 1 : 0;

  Serial.print("ranura: ");
  Serial.print( ranura );
  Serial.print(" -> ");
  Serial.println( config_toString(sct) );
  set_config( ranura, sct );
}

void hs_cli_setup( char **argv, int argc )
{
  DeviceConfig c;
  
  if( argc < 2 )
  {
    EEPROM.get( 0, c );
  } else {
    strncpyln( c.email, argv[0], 50 );
    strncpyln( c.alias, argv[1], 20 );
    EEPROM.put( 0, c );
  }
  Serial.print("Email: "); Serial.println(c.email);
  Serial.print("Alias: "); Serial.println(c.alias);
}

void hs_cli_reload( char **argv, int argc )
{
  Serial.println("[+] Recargando la configuración");
  //load_config();
}

void hs_cli_clear( char **argv, int argc )
{
  
  String temp = "";
  
  Serial.print("Se borrará toda la configuracion, desea continuar S/N?");
  
  while(!Serial.available());
  while(Serial.available()){
    temp.concat( (char)Serial.read() );
    delay(25);
  }

  if( temp != "S" ) {
    Serial.println("\nNo se realizó nungun cambio");
    return;
  }
  
  for (int i = 0 ; i < EEPROM.length() ; i++) {
    EEPROM.write(i, 0);
  }

  for(int i = 0; i < LIMIT_CONFIG_S; i++ )
  {
    set_config( i, EMPTY_CONFIG );
  }

  Serial.println("\nLa configuración se ha restablecido");
}

void hs_cli_reset( char **argv, int argc )
{
  
  String temp = "";
  
  Serial.print("Se borrará toda la configuracion, desea continuar S/N?");
  
  while(!Serial.available());
  while(Serial.available()){
    temp.concat( (char)Serial.read() );
    delay(25);
  }

  if( temp != "S" ) {
    Serial.println("\nNo se realizó nungun cambio");
    return;
  }
  
  for (int i = 0 ; i < EEPROM.length() ; i++) {
    EEPROM.write(i, 0);
  }
  SensorConfig sct = EMPTY_CONFIG;
  
  for(int i = 0; i < LIMIT_CONFIG_S; i++ )
  {
    sprintf(sct.alias,"sen%02d",i);
    set_config( i, sct );
    
  }

  Serial.println("\nLa configuración se ha restablecido");
}


bool hs_cli( String &comando )
{
  char *cmd = NULL; //puntero que apunta a la copia temporal del comando
  char *com = NULL; //puntero al comando
  char *arg = NULL; //puntero al parametro
  char *tok = NULL; //puntero temporal
  
  int argc = 0; //contador de los tokens
  char *argv[5]; //arreglo de punteros a los tokens del comando (token:token:token:token)

  //crea un nuevo estrting
  cmd = new char[ comando.length() ];
  strcpy( cmd, comando.c_str() );

  for( char *ptr = cmd; *ptr != '\0'; ++ptr )
  {
    *ptr = toupper( *ptr );
  }

  com = strtok( cmd,  " "); //obtiene el comando
  arg = strtok( NULL, " "); //obtiene los tokens

  //Serial.print("com: "); Serial.println(com);
  //Serial.print("arg: "); Serial.println(arg);

  tok = strtok( arg, ":");
  if( tok != NULL )
  {
    do{
      argv[ argc++ ] = tok;
    }while( argc < 5 && (tok=strtok(NULL, ":")) != NULL  );
  }

  //comando ALIAS:TIPO:PIN,PIN,PIN:ESTADO
  
  //si el comando es "set"
  if( strcmp( "SET", com ) == 0 ) {
    hs_cli_set( argv, argc );
  } else if( strcmp("GET", com) == 0 ) {
    hs_cli_get( argv, argc );
  } else if( strcmp("DEL", com) == 0 ) {
    hs_cli_del( argv, argc );
  } else if( strcmp("ON", com) == 0 ) {
    hs_cli_on( argv, argc );
  } else if( strcmp("OFF", com) == 0 ) {
    hs_cli_off( argv, argc );
  } else if( strcmp("SETUP", com) == 0 ) {
    hs_cli_setup( argv, argc );
  } else if( strcmp("RELOAD", com) == 0 ) {
    hs_cli_reload( argv, argc );
  } else if( strcmp("RESET", com) == 0 ) {
    hs_cli_reset( argv, argc );
  } else if( strcmp("CLEAR", com) == 0 ) {
    hs_cli_clear( argv, argc );
  } 
  else {
    Serial.println("Comando no reconocido");
  }

  delete[] cmd;
}
