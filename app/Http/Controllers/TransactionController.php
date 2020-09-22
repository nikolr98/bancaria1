<?php

namespace App\Http\Controllers;

use App\Transaction;
use App\User;
use Illuminate\Http\Request;
use DB;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class TransactionController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $this->validate($request,[
            'total' => 'required',

        ]);

        if (( $request->get('tipo') == 1 ) ){
        
            $mytime= Carbon::now('America/Costa_Rica');

            $transaction = new Transaction;
            $transaction->user_id = \Auth::user()->id;
            $transaction->fecha = $mytime->toDateString();
            $transaction->tipo_transaccion = $request->tipo;
            $transaction->total = $request->total;
            $transaction->save();

            $user= User::findOrFail(Auth::user()->id);
            $user->s_actual= auth()->user()->s_actual + $request->total;
            $user->save();

            return redirect('home')->with('success','¡Deposito satisfactorio!');
        
        }else{ 
            
            if ((auth()->user()->s_actual >= $request->get('total') ) ){
        
                if (( $request->get('tipo') == 2 ) ){
            
                    $mytime= Carbon::now('America/Costa_Rica');

                    $transaction = new Transaction;
                    $transaction->user_id = \Auth::user()->id;
                    $transaction->fecha = $mytime->toDateString();
                    $transaction->tipo_transaccion = $request->tipo;
                    $transaction->total = $request->total;
                    $transaction->save();

                    $user= User::findOrFail(Auth::user()->id);
                    $user->s_actual= auth()->user()->s_actual - $request->total;
                    $user->save();


                    return redirect('home')->with('success','¡Retiro satisfactorio!');

                }else{
                    $destino = DB::table('users')
                    ->select('id')
                    ->where('numero_de_cuenta', $request->cuenta_destino)
                    ->get(); 

                        if($destino){
                            $mytime= Carbon::now('America/Costa_Rica');

                            $transaction = new Transaction;
                            $transaction->user_id = \Auth::user()->id;
                            $transaction->fecha = $mytime->toDateString();
                            $transaction->tipo_transaccion= $request->tipo;
                            $transaction->total = $request->total;
                            $transaction->save();
        
                            $user= User::findOrFail(Auth::user()->id);
                            $user->s_actual= auth()->user()->s_actual - $request->total;
                            $user->save();
                           
                            
                            foreach($destino as  $userdestino){
                                $userdestino= User::find($userdestino->id);
                                $userdestino->s_actual= $userdestino->s_actual  + $request->total;
                                $userdestino->save();
                            }
                            return redirect('home')->with('success','¡Retiro satisfactorio!');
                        }else{
                            dd('El numero de cuenta que ingreso no esta registrado');
                        }

                    
                }
                       
                        
                }else{
                    dd('Saldo insuficiciente'); 
                }

        
            return redirect('home')->with('success','¡la saldo de salida es mayor de la existente !');

        
        }
        


            
        
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}