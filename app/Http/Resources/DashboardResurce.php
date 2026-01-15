<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class DashboardResurce extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        return [
            'total_tickets' => $this['total_tickets'],
            'active_tickets' => $this['active_tickets'],
            'resolved_tickets' => $this['resolved_tickets'],
            'avg_resolution_time' => $this['avg_resolution_time'],
            'status_distribution' => [
                'open' => $this['status_distribution']['open'],
                'onprogress' => $this['status_distribution']['onprogress'],
                'resolved' => $this['status_distribution']['resolved'],
                'rejected' => $this['status_distribution']['rejected'],
            ],
        ];
    }
}
