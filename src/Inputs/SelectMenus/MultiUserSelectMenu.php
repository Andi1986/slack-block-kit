<?php

declare(strict_types=1);

namespace Jeremeamia\Slack\BlockKit\Inputs\SelectMenus;

class MultiUserSelectMenu extends SelectMenu
{
    /** @var string[] */
    private $initialUsers;

    /**
     * @param string[] $initialUsers
     * @return static
     */
    public function initialUsers(array $initialUsers): self
    {
        $this->initialUsers = $initialUsers;

        return $this;
    }

    /**
     * @return array
     */
    public function toArray(): array
    {
        $data = parent::toArray();

        if (!empty($this->initialUsers)) {
            $data['initial_users'] = $this->initialUsers;
        }

        return $data;
    }
}
