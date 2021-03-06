<?php

namespace App\Observers;

use App\Comment;
use App\Activity;
use App\Task;
use App\Task_list;
use App\Project;
use App\Milestone;
use App\Board;
use App\File;
use Auth;

class CommentdObserver
{
    use ObserverTrait;
    /**
     * Handle the comment "created" event.
     *
     * @param  \App\Comment  $comment
     * @return void
     */
    public function created(Comment $comment)
    {
        $action_type = 'create';
        $this->log_activity( $comment, $action_type );
    }

    /**
     * Handle the comment "updated" event.
     *
     * @param  \App\Comment  $comment
     * @return void
     */
    public function updated(Comment $comment)
    {
        $this->call_attribute_methods( $comment );
    }

    public function deleting( Comment $comment ) {
        $action_type = 'delete';
        $this->log_activity( $comment, $action_type );
    }

    /**
     * Handle the comment "deleted" event.
     *
     * @param  \App\Comment  $comment
     * @return void
     */
    public function deleted(Comment $comment)
    {
        //
    }

    /**
     * Handle the comment "restored" event.
     *
     * @param  \App\Comment  $comment
     * @return void
     */
    public function restored(Comment $comment)
    {
        //
    }

    /**
     * Handle the comment "force deleted" event.
     *
     * @param  \App\Comment  $comment
     * @return void
     */
    public function forceDeleted(Comment $comment)
    {
        //
    }

    protected function content( Comment $comment, $old_value ) {
        $action_type = 'update';
        $this->log_activity( $comment, $action_type );
    }

    private function log_activity( Comment $comment, $action_type ) {
        $parent_comment = Comment::parent_comment( $comment->id );
        $commentable_type = $parent_comment->commentable_type;
        $commentable = $this->get_commentable( $parent_comment );

        switch ( $commentable_type ) {
            case 'task':
                $this->comment_on_task( $comment, $commentable, $action_type );
                break;

            case 'task_list':
                $this->comment_on_task_list( $comment, $commentable, $action_type );
                break;

            case 'discussion_board':
                $this->comment_on_discussion_board( $comment, $commentable, $action_type );
                break;

            case 'milestone':
                $this->comment_on_milestone( $comment, $commentable, $action_type );
                break;

            case 'project':
                $this->comment_on_project( $comment, $commentable, $action_type );
                break;

            case 'file':
                $this->comment_on_file( $comment, $commentable, $action_type );
                break;
        }
    }

    private function comment_on_task( Comment $comment, Task $task, $action_type ) {
        $meta = [
            'comment_id' => $comment->id,
            'task_title' => $task->title,
        ];

        if ( $action_type == 'create' && $comment->commentable_type == 'comment' ) {
            $action = 'reply_comment_on_task';
        } else if ( $action_type == 'update' && $comment->commentable_type == 'comment' ) {
            $action = 'update_reply_comment_on_task';
        } else if ( $action_type == 'delete' && $comment->commentable_type == 'comment' ) {
            $action = 'delete_reply_comment_on_task';
        } else if ( $action_type == 'create' ) {
            $action = 'comment_on_task';
        } else if ( $action_type == 'delete' ) {
            $action = 'delete_comment_on_task';
        }
        else if ( $action_type == 'update' ) {
            $action = 'update_comment_on_task';
        }

        Activity::create([
            'actor_id'      => Auth::id(),
            'action'        => $action,
            'action_type'   => $action_type,
            'resource_id'   => $task->id,
            'resource_type' => 'task',
            'meta'          => $meta,
            'project_id'    => $comment->project_id,
        ]);
    }

    private function comment_on_task_list( Comment $comment, Task_List $list, $action_type ) {
        $meta = [
            'comment_id' => $comment->id,
            'task_list_title' => $list->title,
        ];

        if ( $action_type == 'create' && $comment->commentable_type == 'comment' ) {
            $action = 'reply_comment_on_task_list';
        } elseif ( $action_type == 'update' && $comment->commentable_type == 'comment' ) {
            $action = 'update_reply_comment_on_task_list';
        } elseif ( $action_type == 'delete' && $comment->commentable_type == 'comment' ) {
            $action = 'delete_reply_comment_on_task_list';
        } elseif ( $action_type == 'create' ) {
            $action = 'comment_on_task_list';
        } elseif ( $action_type == 'update' ) {
            $action = 'update_comment_on_task_list';
        } elseif ( $action_type == 'delete' ) {
            $action = 'delete_comment_on_task_list';
        }

        Activity::create([
            'actor_id'      => Auth::id(),
            'action'        => $action,
            'action_type'   => $action_type,
            'resource_id'   => $list->id,
            'resource_type' => 'task_list',
            'meta'          => $meta,
            'project_id'    => $comment->project_id,
        ]);
    }

    private function comment_on_discussion_board( Comment $comment, Board $board, $action_type ) {
        $meta = [
            'comment_id' => $comment->id,
            'discussion_board_title' => $board->title,
        ];

        if ( $action_type == 'create' && $comment->commentable_type == 'comment' ) {
            $action = 'reply_comment_on_discussion_board';
        } elseif ( $action_type == 'update' && $comment->commentable_type == 'comment' ) {
            $action = 'update_reply_comment_on_discussion_board';
        } elseif ( $action_type == 'delete' && $comment->commentable_type == 'comment' ) {
            $action = 'delete_reply_comment_on_discussion_board';
        } elseif ( $action_type == 'create' ) {
            $action = 'comment_on_discussion_board';
        } elseif ( $action_type == 'update' ) {
            $action = 'update_comment_on_discussion_board';
        } elseif ( $action_type == 'delete' ) {
            $action = 'delete_comment_on_discussion_board';
        }

        Activity::create([
            'actor_id'      => Auth::id(),
            'action'        => $action,
            'action_type'   => $action_type,
            'resource_id'   => $board->id,
            'resource_type' => 'discussion_board',
            'meta'          => $meta,
            'project_id'    => $comment->project_id,
        ]);
    }

    private function comment_on_milestone( Comment $comment, Milestone $milestone, $action_type ) {
        $meta = [
            'comment_id' => $comment->id,
            'milestone_title' => $milestone->title,
        ];

        if ( $action_type == 'create' && $comment->commentable_type == 'comment' ) {
            $action = 'reply_comment_on_milestone';
        } elseif ( $action_type == 'update' && $comment->commentable_type == 'comment' ) {
            $action = 'update_reply_comment_on_milestone';
        }  elseif ( $action_type == 'delete' && $comment->commentable_type == 'comment' ) {
            $action = 'delete_reply_comment_on_milestone';
        } elseif ( $action_type == 'create' ) {
            $action = 'comment_on_milestone';
        } elseif ( $action_type == 'update' ) {
            $action = 'update_comment_on_milestone';
        } elseif ( $action_type == 'delete' ) {
            $action = 'delete_comment_on_milestone';
        }

        Activity::create([
            'actor_id'      => Auth::id(),
            'action'        => $action,
            'action_type'   => $action_type,
            'resource_id'   => $milestone->id,
            'resource_type' => 'milestone',
            'meta'          => $meta,
            'project_id'    => $comment->project_id,
        ]);
    }

    private function comment_on_project( Comment $comment, Project $project, $action_type ) {
        $meta = [
            'comment_id' => $comment->id,
            'project_title' => $project->title,
        ];

        if ( $action_type == 'create' && $comment->commentable_type == 'comment' ) {
            $action = 'reply_comment_on_project';
        } elseif ( $action_type == 'update' && $comment->commentable_type == 'comment' ) {
            $action = 'update_reply_comment_on_project';
        } elseif ( $action_type == 'create' ) {
            $action = 'comment_on_project';
        } elseif ( $action_type == 'update' ) {
            $action = 'update_comment_on_project';
        }

        Activity::create([
            'actor_id'      => Auth::id(),
            'action'        => $action,
            'action_type'   => $action_type,
            'resource_id'   => $project->id,
            'resource_type' => 'project',
            'meta'          => $meta,
            'project_id'    => $comment->project_id,
        ]);
    }

    private function comment_on_file( Comment $comment, File $file, $action_type ) {
        $physical_file = File_System::get_file( $file->attachment_id );

        $meta = [
            'comment_id'    => $comment->id,
            'parent'        => $file->parent,
            'file_url'      => $physical_file['url'],
            'file_title'    => $physical_file['name'] . '.' . $physical_file['file_extension'],
            'attachment_id' => $file->attachment_id,
        ];

        if ( $action_type == 'create' && $comment->commentable_type == 'comment' ) {
            $action = 'reply_comment_on_file';
        } elseif ( $action_type == 'update' && $comment->commentable_type == 'comment' ) {
            $action = 'update_reply_comment_on_file';
        } elseif ( $action_type == 'delete' && $comment->commentable_type == 'comment' ) {
            $action = 'delete_reply_comment_on_file';
        } elseif ( $action_type == 'create' ) {
            $action = 'comment_on_file';
        } elseif ( $action_type == 'update' ) {
            $action = 'update_comment_on_file';
        } elseif ( $action_type == 'delete' ) {
            $action = 'delete_comment_on_file';
        }

        Activity::create([
            'actor_id'      => Auth::id(),
            'action'        => $action,
            'action_type'   => $action_type,
            'resource_id'   => $file->id,
            'resource_type' => 'file',
            'meta'          => $meta,
            'project_id'    => $comment->project_id,
        ]);
    }

    private function get_commentable( Comment $comment ) {
        $commentable = null;

        switch ( $comment->commentable_type ) {
            case 'task':
                $commentable = Task::find( $comment->commentable_id );
                break;

            case 'task_list':
                $commentable = Task_List::find( $comment->commentable_id );
                break;

            case 'discussion_board':
                $commentable = Board::find( $comment->commentable_id );
                break;

            case 'milestone':
                $commentable = Milestone::find( $comment->commentable_id );
                break;

            case 'project':
                $commentable = Project::find( $comment->commentable_id );
                break;

            case 'file':
                $commentable = File::find( $comment->commentable_id );
                break;
        }

        return $commentable;
    }
}
