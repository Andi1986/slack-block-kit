<?php

declare(strict_types=1);

namespace Jeremeamia\Slack\BlockKit\Inputs\SelectMenus;

class SelectMenuFactory extends MenuFactory
{
    public function forStaticOptions(): StaticSelectMenu
    {
        return $this->create(StaticSelectMenu::class);
    }

    public function forExternalOptions(): ExternalSelectMenu
    {
        return $this->create(ExternalSelectMenu::class);
    }

    public function forUsers(): UsersSelectMenu
    {
        return $this->create(UsersSelectMenu::class);
    }

    public function forChannels(): ChannelsSelectMenu
    {
        return $this->create(ChannelsSelectMenu::class);
    }

    public function forConversations(): ConversationsSelectMenu
    {
        return $this->create(ConversationsSelectMenu::class);
    }
}
