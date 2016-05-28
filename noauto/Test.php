<?php

class Test extends PHPUnit_Framework_TestCase
{
    public function testPlugin()
    {
        $obj = new CashDrop();
        $obj->plugin_transaction_reset();
        $this->assertEquals(false, CoreLocal::get('cashDropWarned'));
    }

    public function testNotifier()
    {
        $n = new CashDropNotifier();
        CoreLocal::set('cashDropWarned', false);
        $this->assertInternalType('string', $n->draw());
        CoreLocal::set('cashDropWarned', true);
        $this->assertInternalType('string', $n->draw());
    }

    public function testParser()
    {
        $p = new CashDropParser();
        $this->assertEquals(true, $p->check('DROPDROP'));
        $this->assertEquals(true, $p->check('123DROP'));
        $this->assertEquals(true, $p->check('DROP123'));
        $this->assertEquals(false, $p->check('FOO'));

        $this->assertInternalType('array', $p->parse('DROPDROP'));
        $this->assertInternalType('array', $p->parse('123DROP'));
        $this->assertInternalType('array', $p->parse('DROP123'));
    }

    public function testPreParser()
    {
        $p = new CashDropPreParser();
        CoreLocal::set('cashDropWarned', false);
        CoreLocal::set('standalone', 1);
        CoreLocal::set('cashDropThreshold', 500);

        $this->assertEquals(false, $p->check('foo'));

        CoreLocal::set('cashDropWarned', false);
        CoreLocal::set('standalone', 0);

        $this->assertEquals(false, $p->check('foo'));

        CoreLocal::set('cashDropWarned', false);
        SQLManager::addResult(array(0=>501));

        $this->assertEquals(true, $p->check('foo'));

        $this->assertEquals(false, $p->check('foo'));

        $this->assertEquals('DROPDROPfoo', $p->parse('foo'));
    }

    public function testPage()
    {
        $p = new CashDropWarningPage();
        $this->assertEquals(true, $p->preprocess());
        ob_start();
        $p->body_content();
        $this->assertInternalType('string', ob_get_clean());
    }
}

