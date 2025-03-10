<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Gallery extends Model
{
    public $timestamps = false;

    protected $table = 'galleries';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'id', 'company_id', 'filename', 'order'
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [];


    public function company()
    {
        return $this->belongsTo('App\Company', 'id', 'company_id');
    }

    public static function next($company_id){
		$order = 1;
		$sql = 'select max(`order`) as `order` from galleries where company_id=?';
		$res = \DB::select($sql, [$company_id]);
		if(count($res) == 1 && !is_null($res[0]->order)){
			$order = $res[0]->order + 1;
		}
		return $order;
	}


	public function order_start(){
		$primero = Gallery::orderBy('order', 'asc')->where('company_id', $this->company_id)->first();
		if(!is_null($primero) && $primero->id != $this->id){
			$this->order = $primero->order - 1;
			$this->save();
			$this->reorder();
		}
	}

	public function order_up(){
		$previous = Gallery::orderBy('order', 'desc')->where('company_id', $this->company_id)->where('order', '<', $this->order)->first();
		if(!is_null($previous)){
			$order = $previous->order;
			$previous->order = $this->order;
			$this->order = $order;
			$previous->save();
			$this->save();
			$this->reorder();
		}
	}

	public function order_down(){
		$next = Gallery::orderBy('order', 'asc')->where('company_id', $this->company_id)->where('order', '>', $this->order)->first();
		if(!is_null($next)){
			$order = $next->order;
			$next->order = $this->order;
			$this->order = $order;
			$next->save();
			$this->save();
			$this->reorder();
		}
	}

	public function order_end(){
		$ultimo = Gallery::orderBy('order', 'desc')->where('company_id', $this->company_id)->first();
		if(!is_null($ultimo) && $ultimo->id != $this->id){
			$this->order = $ultimo->order + 1;
			$this->save();
			$this->reorder();
		}
	}

	public function reorder(){
		$order = 1;
		foreach(Gallery::where('company_id', $this->company_id)->orderBy('order', 'asc')->get() as $element){
			$element->order = $order;
			$element->save();
			$order++;
		}
	}

}
