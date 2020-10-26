<?php
Route::get('/', function () {
    return view('welcome');
});



Route::resource('/tipoproducto','TipoProductoController');
Route::get('/buscartipoproductonombre','TipoProductoController@buscartipoproductonombre');

Route::resource('/producto','ProductoController');
Route::post('/producto/image/upload','ProductoController@subirImagen');
Route::get('/producto/image/get/{filename}','ProductoController@getImage');
Route::get('/buscarproductonombre','ProductoController@buscarproductonombre');
Route::get('/buscarproductocodigo','ProductoController@buscarproductocodigo');

Route::get('/buscarproductotipo','ProductoController@buscarproductotipo');

Route::get('/stockminimo','ProductoController@stockminimo');



Route::resource('/persona','PersonaController');
Route::post('/persona/image/upload','PersonaController@subirImagen');
Route::get('/persona/image/get/{filename}','PersonaController@getImage');
Route::get('/buscarpersonanombre','PersonaController@buscarpersonanombre');
Route::get('/buscarpersonacodigo','PersonaController@buscarpersonacodigo');


Route::resource('/proveedor','ProveedorrController');
Route::post('/proveedor/image/upload','ProveedorrController@subirImagen');
Route::get('/proveedor/image/get/{filename}','ProveedorrController@getImage');
Route::get('/buscarproveedorcodigo','ProveedorrController@buscarproveedorcodigo');

Route::get('/buscarproveedornombre','ProveedorrController@buscarproveedornombre');




Route::resource('/usuario','UsuarioController');
Route::post('/usuario/image/upload','UsuarioController@subirImagen');
Route::get('/usuario/image/get/{filename}','UsuarioController@getImage');
Route::get('/buscarusuarionombre','UsuarioController@buscarusuarionombre');


Route::post('/login','UsuarioController@login');
Route::put('/updateUserLog','UsuarioController@updateUserLog');
Route::post('/uploadImage/update','UsuarioController@uploadImage');
Route::get('/getImageUser/{filename}','UsuarioController@getImageUser');
Route::put('/updatePassUserLog','UsuarioController@updatePassUserLog');
Route::post('/register','UsuarioController@register');


Route::resource('/configuracion','ConfiguracionController');
Route::get('/getiva','ConfiguracionController@getiva');


Route::resource('/ingreso','IngresoController');
Route::get('/buscaringreso','IngresoController@buscaringreso');
Route::get('/numeroingreso','IngresoController@numIngreso');


Route::resource('/venta', 'VentaController');
Route::get('/buscarventa','VentaController@buscarventa');
Route::get('/numcomprobante','VentaController@numcomprobante');


Route::get('/productosPdf','ProductoController@productosPdf');
Route::get('/ingresosPdf','IngresoController@ingresosPdf');
Route::get('/ventasPdf','VentaController@ventasPdf');


Route::get('/dashboard','DashboardController');


Route::get('/notifiuserunread','UsuarioController@userUnreadNotification');
Route::get('/notifiusermarkAsread','UsuarioController@userReadNotification');



Route::resource('/pedido','PedidoClienteController');
Route::get('/buscarpedido','PedidoClienteController@buscaringreso');
Route::get('/numeroingreso','PedidoClienteController@numIngreso');

Route::get('/mispedidos','PedidoClienteController@misPedidos');

