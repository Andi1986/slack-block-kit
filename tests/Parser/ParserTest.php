<?php

declare(strict_types = 1);

use Jeremeamia\Slack\BlockKit\Blocks\Actions;
use Jeremeamia\Slack\BlockKit\Blocks\{Context,Divider,Image,Section};
use Jeremeamia\Slack\BlockKit\Inputs\{Button,DatePicker};
use \Jeremeamia\Slack\BlockKit\Tests\TestCase;
use \Jeremeamia\Slack\BlockKit\Slack;
use \Jeremeamia\Slack\BlockKit\Parser\Parser;
use \Jeremeamia\Slack\BlockKit\Partials\Confirm;
use \Jeremeamia\Slack\BlockKit\Surfaces\Message;

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

    public function testTwoColTable()
    {

        $msg = Slack::newMessage();
        $msg->newTwoColumnTable('table1')
            ->caption('My Kids')
            ->cols('Name', 'Age')
            ->row('Joey', '10')
            ->row('Izzy', '8')
            ->row('Livy', '5')
            ->row('Emmy', '0');
        $msg->divider();
        $msg->newTwoColumnTable('table2')
            ->caption('My Kids')
            ->cols('Name', 'Age')
            ->rows([
                ['Joey', '10'],
                ['Izzy', '8'],
                ['Livy', '5'],
                ['Emmy', '0'],
            ]);
        $msg->divider();
        $msg->newTwoColumnTable('table3')
            ->caption('My Kids')
            ->cols('Name', 'Age')
            ->rows([
                'Joey' => '10',
                'Izzy' => '8',
                'Livy' => '5',
                'Emmy' => '0',
            ]);


        $array = $msg->toArray();
        $obj = Parser::parse($array);
        $this->assertEquals($array, $obj->toArray());

    }

    public function testMessageChained()
    {

        $msg = Message::new()
            ->add(Section::new()
                ->blockId('b1')
                ->mrkdwnText('*foo* _bar_')
                ->fieldMap(['foo' => 'bar', 'fizz' => 'buzz'])
                ->setAccessory(Button::new()
                    ->actionId('a1')
                    ->text('Click me!')
                    ->value('two')))
            ->add(Divider::new()
                ->blockId('b2'))
            ->add(Image::new()
                ->blockId('b3')
                ->title('This meeting has gone off the rails!')
                ->url('https://i.imgflip.com/3dezi8.jpg')
                ->altText('A train that has come of the railroad tracks'))
            ->add(Context::new()
                ->blockId('b4')
                ->image('https://i.imgflip.com/3dezi8.jpg', 'off the friggin rails again')
                ->mrkdwnText('*foo* _bar_'))
            ->text('Hello!', 'b5')
            ->add(Actions::new()
                ->blockId('b6')
                ->add(Button::new()
                    ->actionId('a2')
                    ->text('Submit')
                    ->value('Hi!'))
                ->add(DatePicker::new()
                    ->placeholder('Choose a date')
                    ->initialDate('2020-01-01')
                    ->confirm('Proceed?', 'If this is correct, click "OK".')));


        $array = $msg->toArray();
        print_r($array);

        $obj = Parser::parse($array);

        print_r($obj->toArray());
        $this->assertEquals($array, $obj->toArray());

    }


}
