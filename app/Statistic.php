<?php

namespace App;

use App\Http\Helpers\StatisticActions;
use Carbon\Carbon;
use Doctrine\DBAL\Query\QueryBuilder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use MongoDB\Driver\Query;

class Statistic extends Model
{
    protected $fillable = [
    	'popup_id',
	    'viewed',
	    'closed',
	    'clicked'
    ];

    protected $appends = [
//    	'action'
    ];

    protected $hidden = [
        'viewed',
	    'closed',
	    'clicked'
    ];

    public function scopeClosed($query) {
		$query->where('closed', true);
    }

    public function scopeClicked($query) {
		$query->where('clicked', true);
    }

    public function scopeViewed($query) {
	    $query->where('viewed', true);
    }

    public function popup() {
    	return $this->belongsTo(Popup::class);
    }

	/**
	 * @deprecated
	 * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
	 */
    public function event() {
    	return $this->belongsTo(Event::class);
    }

    public function scopeDateFrom($query, $dateFrom) {
	    $query->where('created_at', '>=', Carbon::parse($dateFrom)->format('d/m/Y 00:00:00'));
    }

    public function scopeDateTo($query, $dateTo) {
	    $query->where('created_at', '<=', Carbon::parse($dateTo)->format('d/m/Y 23:59:59'));
    }

    public function scopeActions($query, $action) {
    	$query->where($action, true);
    }

    public function scopeFilter($query, Array $filter) {
    	if (isset($filter['date_from'])) {
			$query->dateFrom($filter['date_from']);
	    }
	    if (isset($filter['date_to'])) {
		    $query->dateTo($filter['date_to']);
	    }
	    if (isset($filter['action'])) {
	    	$action = StatisticActions::getAction($filter['action']);
			$query->actions($action);
	    }
    }

    public function getActionAttribute() {
    	if ($this->viewed) {
    		return 'viewed';
	    } elseif ($this->closed) {
    		return 'closed';
	    } elseif ($this->clicked) {
    		return 'clicked';
	    }
    }

    public static function calculate($popup_id, $filter) {
    	$query = self::where('popup_id', $popup_id)
		    ->filter($filter);

    	$date_format = '%Y %m %d';

    	if (isset($filter['group_by'])) {
    		switch ($filter['group_by']) {
			    case 1:
			    	$date_format = '%Y-%m-%d';
			    	break;

			    case 2:
			    	$date_format = '%Y-%m';
			    	break;

			    default:
			    	$date_format = '%Y-%m-%d';
			    	break;
		    }
	    }

    	if (isset($filter['action'])) {
		    $action = StatisticActions::getAction($filter['action']);
		    $arr = self::select([
			    DB::raw('DATE_FORMAT(created_at, "'.$date_format.'") as day'),
			    DB::raw('SUM('.$action.') as '.$action.'_count')
		    ])->groupBy('day')->get();

		    $total = [
			    $action . '_count' => $arr->sum($action . '_count')
		    ];

		    return [
			    'dates' => $arr->groupBy('day')->all(),
			    'total' => $total
		    ];

	    } else {

			$arr = self::select([
				DB::raw('DATE_FORMAT(created_at, "'. $date_format .'") as day'),
				DB::raw('SUM(closed) as closed_count'),
				DB::raw('SUM(viewed) as viewed_count'),
				DB::raw('SUM(clicked) as clicked_count'),
			])->groupBy('day')->get();//->groupBy('day')->all();

		    $total = [
			    'closed_count' => $arr->sum('closed_count'),
			    'clicked_count' => $arr->sum('clicked_count'),
			    'viewed_count' => $arr->sum('viewed_count')
		    ];

			return [
			    'dates' => $arr->groupBy('day')->all(),
			    'total' => $total
		    ];
	    }
    }
}
