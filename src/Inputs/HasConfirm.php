<?php

declare(strict_types=1);

namespace Jeremeamia\Slack\BlockKit\Inputs;

use Jeremeamia\Slack\BlockKit\Partials\Confirm;

trait HasConfirm
{
    /** @var Confirm */
    private $confirm;

    /**
     * @param Confirm $confirm
     * @return static
     */
    public function setConfirm(Confirm $confirm): self
    {
        $this->confirm = $confirm->setParent($this);

        return $this;
    }

    /**
     * @param string $title
     * @param string $text
     * @param string $confirm
     * @param string $deny
     * @return static
     */
    public function confirm(string $title, string $text, string $confirm = 'OK', string $deny = 'Cancel'): self
    {
        return $this->setConfirm(new Confirm($title, $text, $confirm, $deny));
    }

    /**
     * @param array $content
     * @return void
     */
    protected function parseConfirm(array &$content): void
    {

        if (! isset($content['confirm'])) {
            return;
        }

        $this->setConfirm((new Confirm())->parse($content['confirm']));
        unset($content['confirm']);

    }


}
