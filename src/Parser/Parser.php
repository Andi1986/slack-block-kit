<?php

declare(strict_types = 1);

namespace Jeremeamia\Slack\BlockKit\Parser;
use Jeremeamia\Slack\BlockKit\Surfaces\{AppHome, Message, Modal};
use Jeremeamia\Slack\BlockKit\Exception;

class Parser
{

    public static function parse($content) {

        $type = $content['type'] ?? null;
        unset($content['type']);

        switch ($type) {

            case 'home':
                return (new AppHome())->parse($content);

            case 'modal':
                return (new Modal())->parse($content);

            default:
                return (new Message())->parse($content);

        }

    }

}

