<?php

namespace App\Http\Controllers\Account;

use App\Events\User\UserSubscribedToEmailNotification;
use App\Http\Controllers\Controller;
use App\Mail\Account\Unsubscribed;
use App\Mail\TranslateSession;
use App\Models\User;
use Auth;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Mail;


class AccountsController extends Controller
{
	public function update(Request $request)
    {
    	$request->user()->update($request->only(['name']));
    	$this->validate($request, [
            'avatar_id' => Rule::exists('images', 'id')->where(function ($q) use ($request) {
                $q->where('user_id', $request->user()->id);
            }),
        ]);
    	$request->user()->update($request->only(['name', 'avatar_id']));
    	return back();
    }

	public function subscribeToNotifications(Request $request)
	{
		$user = Auth::user();
		if ($request->subscribe === true) {
			$user->email = $request->email;
			$user->save();
			event(new UserSubscribedToEmailNotification($user)); 
		}
		else{
			$user->subscribed = 0;
			$user->save();
		}
			return response()->json($user, 200);
	}
	public function activate(Request $request)
	{

		$user = User::where('email', $request->email)->where('token', $request->token)->firstOrFail();
		$user->subscribed = true;
		$user->save();

	}
	public function loginViaToken(Request $request)
	{
			$user = User::where('id', $request->id)->where('token', $request->token)->firstOrFail();

			Auth::loginUsingId($user->id);
			$location =  $user->mToday()->location;
			return view('pinwall', compact('location'))->withSuccess('Du hast dich erfolgreich eingeloggt');

	} 

	public function onesignalidAdd(Request $request)
	{
		$user = User::find($request->user_id);
		$user->one_signal_player_id = $request->one_signal_player_id;
		$user->subscribed = 1;
		$user->save();
		return response()->json($user, 200);
	}
	public function translateViaMail(Request $request)
	{
		$user = User::find($request->id);
		$user->email = $request->email;
		$user->save;
		Mail::to($request->email)->send(new TranslateSession($user));
	}
	public function unsubscirbeFromEmail(Request $request)
	{
		$token = $request->t;
		$id = $request->i;

		$user = User::find($id);
		if ($user->token === $token){
			$user->subscribed = 0;
			$user->save();
			Mail::to($user->email)->send(new Unsubscribed);
			return view('unsubscribed');
		}


	}
}
