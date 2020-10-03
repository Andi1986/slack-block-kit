<?php

declare(strict_types = 1);

use \Jeremeamia\Slack\BlockKit\Tests\TestCase;
use \Jeremeamia\Slack\BlockKit\Slack;
use \Jeremeamia\Slack\BlockKit\Parser\Parser;
use \Jeremeamia\Slack\BlockKit\Partials\Confirm;

/**
 * @covers \Jeremeamia\Slack\BlockKit\Surfaces\Surface
 */
class ParserTest extends TestCase
{

    public function testModal()
    {

        $msg = Slack::newModal()
            ->title('My Modal')
            ->submit('Submit')
            ->close('Cancel')
            ->privateMetadata('foo=bar')
            ->text('Hello!', 'b1');
        $msg->newInput('b2')
            ->label('Date')
            ->newDatePicker('a1')
            ->placeholder('Choose a date')
            ->initialDate('2020-01-01');
        $msg->newInput('c1')
            ->label('Multiline')
            ->newTextInput('text_input')
            ->placeholder('Text Input')
            ->multiline(true)
            ->minLength(10)
            ->maxLength(100);
        $msg->newInput('c2')
            ->label('Radio Buttons')
            ->newRadioButtons('radio_buttons')
            ->option('foo', 'foo')
            ->option('bar', 'bar', true)
            ->option('foobar', 'foobar')
            ->setConfirm(new Confirm('Switch', 'Do you really want to switch?', 'Yes switch'));
        $msg->newInput('c3')
            ->label('Checkboxes')
            ->newCheckboxes('checkboxes')
            ->option('foo', 'foo')
            ->option('bar', 'bar', true)
            ->option('foobar', 'foobar', true)
            ->setConfirm(new Confirm('Switch', 'Do you really want to switch?', 'Yes switch'));


        $array = $msg->toArray();
        $obj = Parser::parse($array);
        $this->assertEquals($array, $obj->toArray());

    }

    public function testMenus()
    {

        $msg = Slack::newMessage();
        $actions = $msg->newActions('b1');
        $actions->newSelectMenu('m1')
            ->forStaticOptions()
            ->placeholder('Choose a letter?')
            ->options([
                'a' => 'x',
                'b' => 'y',
                'c' => 'z',
            ]);
        $actions->newSelectMenu('m2')
            ->forUsers()
            ->placeholder('Choose a user...');
        $msg->newActions('b2')
            ->newSelectMenu('m3')
            ->forStaticOptions()
            ->placeholder('Choose a letter?')
            ->option('a', 'x')
            ->option('b', 'y', true)
            ->option('c', 'z');
        $msg->newActions('b3')
            ->newSelectMenu('m4')
            ->forStaticOptions()
            ->placeholder('Choose a letter?')
            ->optionGroups([
                'Letters' => [
                    'a' => 'l1',
                    'b' => 'l2',
                    'c' => 'l3',
                ],
                'Numbers' => [
                    '1' => 'n1',
                    '2' => 'n2',
                    '3' => 'n3',
                ]
            ])
            ->initialOption('b', 'l2');
        $msg->newSection('b4')
            ->mrkdwnText('Select some letters and numbers')
            ->newMultiSelectMenuAccessory('m5')
            ->forStaticOptions()
            ->placeholder('Choose a letter?')
            ->setMaxSelectedItems(2)
            ->optionGroups([
                'Letters' => [
                    'a' => 'l1',
                    'b' => 'l2',
                    'c' => 'l3',
                ],
                'Numbers' => [
                    '1' => 'n1',
                    '2' => 'n2',
                    '3' => 'n3',
                ]
            ])
            ->initialOptions(['b' => 'l2', 'c' => 'l3']);
        $msg->newSection('b5')
            ->mrkdwnText('Select from Overflow Menu')
            ->newOverflowMenuAccessory('m6')
            ->option('foo', 'foo')
            ->urlOption('bar', 'bar', 'https://example.org')
            ->option('foobar', 'foobar')
            ->setConfirm(new Confirm('Choose', 'Do you really want to choose this?', 'Yes choose'));


        $array = $msg->toArray();
        $obj = Parser::parse($array);
        $this->assertEquals($array, $obj->toArray());

    }


}
