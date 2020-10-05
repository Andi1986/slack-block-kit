<?php

declare(strict_types=1);

namespace Jeremeamia\Slack\BlockKit\Inputs\SelectMenus;

class MultiSelectMenuFactory extends MenuFactory
{
    public function forStaticOptions(): MultiStaticSelectMenu
    {
        return $this->create(MultiStaticSelectMenu::class);
    }

    public function forExternalOptions(): MultiExternalSelectMenu
    {
        return $this->create(MultiExternalSelectMenu::class);
    }

    public function forUsers(): MultiUsersSelectMenu
    {
        return $this->create(MultiUsersSelectMenu::class);
    }

    public function forChannels(): MultiChannelsSelectMenu
    {
        return $this->create(MultiChannelsSelectMenu::class);
    }

    public function forConversations(): MultiConversationsSelectMenu
    {
        return $this->create(MultiConversationsSelectMenu::class);
    }
}
