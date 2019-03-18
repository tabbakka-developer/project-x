<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;

class Event extends Model
{
    //
	protected $fillable = [
		'name',
		'url_from',
		'desktop_placement',
		'mobile_placement',
		'delay',
		'repeat',
		'time',
		'close',
		'new_tab',
		'round',
		'title_color',
		'background_color',
		'message_color',
		'user_id',
		'template_id'
	];

	protected $appends = [
		'script',
		'size'
	];

	public function template() {
		return $this->belongsTo(Template::class);
	}

	public function getScriptAttribute() {
		$appendData =
			"<script 
					id=\"project_x_script\" 
					async type=\"text/javascript\" 
					src='". asset('js/popup_script.js') ."'>
				</script>" .
			"<meta name=\"hash\" content='" . $this->hash ."'>";

		return $appendData;
	}

	public function getSizeAttribute() {
		return $this->size();
	}

	public function user() {
		return $this->belongsTo(User::class);
	}

	public function statistic() {
		return $this->hasMany(Statistic::class);
	}

	public function size() {
		return $this->popups()->count();
	}

	public function addPopup($data) {
		$data['event_id'] = $this->id;
		$popup = Popup::create($data);
		return $popup;
	}

	public function removePopup($data) {
		try {
			Popup::where('event_id', $this->id)->where('id', $data['id'])->delete();
			return true;
		} catch (\Exception $exception) {
			Log::error($exception->getMessage());
			return false;
		}
	}

	public function clearPopups() {
		try {
			Popup::where('event_id', $this->id)->delete();
		} catch (\Exception $exception) {
			Log::error($exception->getMessage());
			return false;
		}
	}

	public static function createWithChildes($data) {
		DB::beginTransaction();
		try {
			/** @var Event $event */
			$event = self::create($data);
			$hash = Hash::make($event->created_at);
			$event->hash = $hash;
			$event->save();
			foreach ($data['popups'] as $popup) {
				$event->addPopup($popup);
			}
			DB::commit();
			return $event->with('popups')->first();
		}
		catch (\Exception $exception) {
			dd($exception);
			Log::error($exception->getMessage());
			DB::rollBack();
			return false;
		}
	}

	public function updateWithChildes($data) {
		try {
			/** @var Event $event */
			$event = self::update($data);
			$event->clearPopups();

			foreach ($data['popups'] as $popup) {
				$event->addPopup($popup);
			}
			$event->save();
			return $event->with(Popup::class);
		}
		catch (\Exception $exception) {
			Log::error($exception->getMessage());
			return false;
		}
	}

	public function delete() {
		if(file_exists($this->image)){
			@unlink($this->image);
		}
		return parent::delete();
	}

	public function popups() {
		return $this->hasMany(Popup::class);
	}
}
