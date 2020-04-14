<?php

namespace App\Transformers;

use App\Milestone;
use League\Fractal\TransformerAbstract;
use League\Fractal\Pagination\IlluminatePaginatorAdapter;
use Illuminate\Pagination\Paginator;

class MilestoneTransformer extends TransformerAbstract {

    use ResourceEditors;

    protected $defaultIncludes = [
        'creator', 'updater'
    ];

    protected $availableIncludes = [
        'discussion_boards', 'task_lists'
    ];

    public function transform( Milestone $item ) {
        return [
            'id'           => (int) $item->id,
            'title'        => $item->title,
            'description'  => $item->description,
            'order'        => (int) $item->order,
            'achieve_date' => $item->achieve_date,
            'achieved_at'  => $item->updated_at,
            'status'       => $item->status,
            'created_at'   => $item->created_at,
        ];

        
    }


    public function includeTaskLists( Milestone $item ) {
        $page = isset( $_GET['task_list_page'] ) ? intval( $_GET['task_list_page'] ) : 1;

        Paginator::currentPageResolver(function () use ($page) {
            return $page;
        }); 

        $task_lists = $item->task_lists();
        $task_lists = apply_filters('pm_task_list_query', $task_lists, $item->project_id, $item );
        $task_lists = $task_lists->orderBy( 'created_at', 'DESC' )
            ->paginate( 10 );

        $task_list_collection = $task_lists->getCollection();
        $resource = $this->collection( $task_list_collection, new TaskListTransformer );

        $resource->setPaginator( new IlluminatePaginatorAdapter( $task_lists ) );

        return $resource;
    }

    public function includeDiscussionBoards( Milestone $item ) {
        $page = isset( $_GET['discussion_page'] ) ? intval( $_GET['discussion_page'] ) : 1;

        Paginator::currentPageResolver(function () use ($page) {
            return $page;
        }); 

        $discussion_boards = $item->discussion_boards();
        $discussion_boards = apply_filters( 'pm_discuss_query', $discussion_boards, $item->project_id, $item );
        $discussion_boards = $discussion_boards->orderBy( 'created_at', 'DESC' )
            ->paginate( 10 );

        $discussion_board_collection = $discussion_boards->getCollection();
        $resource = $this->collection( $discussion_board_collection, new DiscussionBoardTransformer );

        $resource->setPaginator( new IlluminatePaginatorAdapter( $discussion_boards ) );

        return $resource;
    }
}