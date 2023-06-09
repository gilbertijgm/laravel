<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\DB;
use PhpParser\Node\Stmt\TryCatch;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::get('/', function () {
    return view('welcome');
});

Route::view(uri:'/hola', view: 'saludo');

Route::view(uri:'/inicio', view:'inicio');

//CRUD de regiones****************************************

Route::get('/regiones', function() //read************************************
{
    //obtnemos listado de regiones
   /* $regiones = DB::select('SELECT idRegion, regNombre
                            FROM regiones');*/

    //$regiones = DB::table('regiones')->simplePaginate(4); //simple paginate

    $regiones = DB::table('regiones')->paginate(4);

    return view('regiones', [ 'regiones'=>$regiones ]);
});

Route::get('/region/create', function()
{

    return view('regionCreate');
});

Route::post('/region/store', function() //create*****************************
{
    //diferentes formas de capturar datos enviado por el form:
    //$regNombre = $_POST['regNombre'];
    //$regNombre = request()->input(key:'regNombre');
    //$regNombre = request()->regNombre;
    $regNombre = request(key:'regNombre');

    //insertamos dato en tabla regiones

    try{
        DB::insert(
            'INSERT INTO regiones ( regNombre )
                    VALUES ( :regNombre)',
                    [ $regNombre ]
        );
        //rediereccion con mensje de ok
        return redirect('/regiones')
                ->with([
                    'mensaje'=>'Region: '.$regNombre.' agregada correctamente',
                    'css'=>'success'
                ]);
    } catch ( Throwable $th ){

        //rediereccion con mensje de errores

        return redirect('/regiones')
                ->with([
                    'mensaje'=>'No se pudo agregar la region: '.$regNombre,
                    'css'=>'danger'
                ]);
    }
});

Route::get('/region/edit/{id}', function( $id ) //update********************************
{
    //obtenemos datos de la region a modificar
    /*$region = DB::select('SELECT idRegion, regNombre
                            FROM regiones
                            WHERE idRegion = :id',
                            [ $id ]
              ); //raw SQL*/

    /*fluent query builder*/
    $region = DB::table('regiones')
                    ->where('idRegion', $id)->first();

    //retornamos a la vista del form
    return view('regionEdit', [ 'region'=>$region ]);
});

Route::post('/region/update', function() //update 2*************************************++
{
    //capturar datos
    $regNombre = request(key:'regNombre');
    $idRegion = request(key:'idRegion');

    try{
       /* DB::update('UPDATE regiones
                        SET regNombre = :regNombre
                        WHERE idRegion = :idRegion',
                        []
        ); row SQL*/

        DB::table('regiones')
                ->where('idRegion', $idRegion)
                ->update( ['regNombre'=>$regNombre] );
        //rediereccion con mensje de ok
        return redirect('/regiones')
                ->with([
                    'mensaje'=>'Region: '.$regNombre.' modificada correctamente',
                    'css'=>'success'
                ]);
    } catch ( Throwable $th ){

        //rediereccion con mensje de errores

        return redirect('/regiones')
                ->with([
                    'mensaje'=>'No se pudo modificar la region: '.$regNombre,
                    'css'=>'danger'
                ]);
    }
});


Route::get('/region/delete/{id}', function ($idRegion) //delete******************************+
{
    //obtenemos datos de una region por su id
    $region = DB::table('regiones')
                ->where('idRegion', $idRegion)->first();

    //chuequemos si hay destinos relacionados
    $cantidad = DB::table('destinos')
                ->where('idRegion', $idRegion)->count();

    if( $cantidad > 0){
        return redirect('/regiones')
                ->with(
                    [
                        'mensaje'=>'Nose puede eliminar: '.$region->regNombre.' porque tiene destinos relacionados',
                        'css'=>'warning'
                    ]
                );
    }

    return view('regionDelete', [ 'region'=>$region, 'cantidad'=>$cantidad ]);
});

Route::post('/region/destroy', function() //delete 2: destroy******************************
{
        //capturar datos
        $regNombre = request(key:'regNombre');
        $idRegion = request(key:'idRegion');

        try{
            DB::table('regiones')->where('idRegion', $idRegion)
                                 ->delete();
            //redireccion con mensaje ok
            return redirect('/regiones')
                            ->with([
                                     'mensaje'=>'Region: '.$regNombre.' Eliminada correctamente',
                                     'css'=>'success'
                            ]);
         } catch ( Throwable $th ){

             //rediereccion con mensje de errores

             return redirect('/regiones')
                     ->with([
                         'mensaje'=>'No se pudo eliminar la region: '.$regNombre,
                         'css'=>'danger'
                     ]);
         }
});


//CRUD DE DESTINOS****************************************************
Route::get('/destinos', function()  //read*******************************
{
    //obtenemos listado de destinos
    /*$destinos = DB::select('SELECT *, regNombre FROM destinos as d JOIN regiones as r ON d.idRegiones = r.idRegiones');  ROW SQL*/

    $destinos = DB::table('destinos as d')
    ->join('regiones as r', 'd.idRegion', '=', 'r.idRegion')
    ->paginate(5);



    return view('destinos', [ 'destinos'=>$destinos ]);
});


Route::get('/destino/create', function() //create****************************************
{
    $regiones = DB::table('regiones')->get();

    return view('destinoCreate', [ 'regiones'=>$regiones]);
});

Route::post('/destino/store', function()
{
    //capturamos los datos enviados por el form
    $destNombre = request('destNombre');
    $idRegion =  request('idRegion');
    $destPrecio = request('destPrecio');
    $destAsientos = request('destAsientos');
    $destDisponibles = request('destDisponibles');

    try{
        DB::table('destinos')
        ->insert(
            [
                'destNombre'=>$destNombre,
                'idRegion'=>$idRegion,
                'destPrecio'=>$destPrecio,
                'destAsientos'=>$destAsientos,
                'destDisponibles'=>$destDisponibles
            ]
        );

        return redirect('/destinos')
        ->with([
            'mensaje'=>'Destino: '.$destNombre.' agregado correctamente',
            'css'=>'succes'
        ]);
    }
    catch( Throwable $th )
    {

        return redirect('/destinos')
        ->with([
            'mensaje'=>'No se pudo agregar el destino: '.$destNombre,
            'css'=>'danger'
        ]);
    }

});

Route::get('/destino/edit/{id}', function( $id ) //update*****************************
{
    //obtemos destinos por su id
    $destinos = DB::table('destinos')->where('idDestino', $id)->first();
    //obtenemos listado de regiones
    $regiones = DB::table('regiones')->get();
    return view('destinoEdit', [
        'destinos'=>$destinos,
        'regiones'=>$regiones
    ]);
});


Route::post('/destino/update', function ()
{
    //obtenemos datos
    $idDestino = request('idDestino');
    $destNombre = request('destNombre');
    $idRegion =  request('idRegion');
    $destPrecio = request('destPrecio');
    $destAsientos = request('destAsientos');
    $destDisponibles = request('destDisponibles');

    try{

        DB::table('destinos')->where('idDestino', $idDestino)->update(
            [
                'destNombre'=>$destNombre,
                'idRegion'=>$idRegion,
                'destPrecio'=>$destPrecio,
                'destAsientos'=>$destAsientos,
                'destDisponibles'=>$destDisponibles
            ]
        );

        return redirect('/destinos')
        ->with([
            'mensaje'=>'Destino: '.$destNombre.' modificado correctamente',
            'css'=>'succes'
        ]);
   }
    catch( Throwable $th )
    {

        return redirect('/destinos')
        ->with([
            'mensaje'=>'No se pudo modificar el destino: '.$destNombre,
            'css'=>'danger'
        ]);
    }
});

Route::get('/destino/delete/{id}', function ($id)  //Delete************************
{
    $destino = DB::table('destinos as d')
            ->join('regiones as r', 'd.idRegion', '=', 'r.idRegion')
            ->where('idDestino', $id)
            ->first();

    return view('destinoDelete', [ 'destino'=>$destino]);
});

Route::post('/destino/destroy', function()
{
    //obtenemos los datos
    $idDestino = request('idDestino');
    $destNombre = request('destNombre');

    try{

        DB::table('destinos')
            ->where('idDestino', $idDestino)
            ->delete();

        //redireccion con mensaje ok
        return redirect('/destinos')
                        ->with([
                                 'mensaje'=>'Destino: '.$destNombre.' Eliminada correctamente',
                                 'css'=>'success'
                        ]);
     } catch ( Throwable $th ){

         //rediereccion con mensje de errores

         return redirect('/destinos')
                 ->with([
                     'mensaje'=>'No se pudo eliminar el destino: '.$destNombre,
                     'css'=>'danger'
                 ]);
     }
});
