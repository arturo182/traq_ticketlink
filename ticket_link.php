<?php
namespace traq\plugins;

use avalon\core\Kernel;
use traq\models\Project;
use traq\models\Ticket;
use FishHook;
use HTML;

class TicketLink extends \traq\libraries\Plugin
{
    protected static $info = array(
        'name'    => 'TicketLink',
        'version' => '0.1',
        'author'  => 'arturo182'
    );
    
    public static function init()
    {
        FishHook::add('function:format_text', function(&$text, $strip_html)
        {
			$text = preg_replace_callback('/#([0-9]+)/', function($matches)
			{
				$project = Kernel::app()->project;
				if(!$project)
					return $matches[0];
					
				$ticket = Ticket::select()->where(array(
					array('project_id', $project->id), 
					array('ticket_id', $matches[1])
				))->exec()->fetch();
				
				if(!$ticket)
					return $matches[0];
				
				return HTML::link('<span title="' . $ticket->summary . '">' . $matches[0] . '</span>', $ticket->href());
			}, $text);
        });
    }
}
?>