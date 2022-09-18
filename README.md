Api creada en symfony 6 con metod get para traer la lista de peliculas y los detalles de la base de datos, los cuales han sido previamente almacenados desde la api TMDB 
y un metodo post para enviar una valoración recogida en el front directamente a la api TMDB.

Para la creación de los metodos se ha utilizado el componente HTML Foundation. 

Para que el método post funcione correctamente es necesario cambiar la variable "codigoInvitado" que se encuentra en la funcion "valoración". El código se puede conseguir
en el siguiente Link: https://api.themoviedb.org/3/authentication/guest_session/new?api_key=8f781d70654b5a6f2fa69770d1d115a3, al acceder a éste link se nos abre un 
archivo json y el código que necesitamos se encuentra con la clave: guest_session_id". Éste código solo es válido durante 24 horas, por lo cual si se quiere probar tras 
unos dias será necesario solicitar el código de nuevo. 

También se ha intentado hacer algo de testing mediante PHPUnit.
