<?php

namespace GitBalocco\LaravelUiUtils\Tests\Http;

use GitBalocco\LaravelUiUtils\Http\IdentityHandler;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Orchestra\Testbench\TestCase;

/**
 * @coversDefaultClass \GitBalocco\LaravelUiUtils\Http\IdentityHandler
 * GitBalocco\LaravelUiUtils\Tests\Http\IdentityHandlerTest
 */
class IdentityHandlerTest extends TestCase
{
    /** @var $testClassName as test target class name */
    protected $testClassName = IdentityHandler::class;

    /**
     * @covers ::__construct
     */
    public function test___construct()
    {
        $request = \Mockery::mock(Request::class);
        $view = \Mockery::mock(View::class);
        $targetClass = new $this->testClassName($request, $view);

        \Closure::bind(
            function () use ($targetClass, $request, $view) {
                //assertions
                $this->assertSame($request, $targetClass->request);
                $this->assertSame($view, $targetClass->view);
            },
            $this,
            $targetClass
        )->__invoke();
        return $targetClass;
    }

    /**
     * @param mixed $targetClass
     * @covers ::enableInput
     * @covers ::disableInput
     * @depends test___construct
     */
    public function test_enableAndDisableInput($targetClass)
    {
        \Closure::bind(
            function () use ($targetClass) {
                //テスト対象メソッドの実行
                $actual = $targetClass->enableInput();
                //assertions
                $this->assertSame($targetClass->enableInput, true);
                $this->assertInstanceOf(IdentityHandler::class, $actual);

                //テスト対象メソッドの実行
                $actual = $targetClass->disableInput();
                //assertions
                $this->assertSame($targetClass->enableInput, false);
                $this->assertInstanceOf(IdentityHandler::class, $actual);

                //テスト対象メソッドの実行
                $actual = $targetClass->enableInput();
                //assertions
                $this->assertSame($targetClass->enableInput, true);
                $this->assertInstanceOf(IdentityHandler::class, $actual);
            },
            $this,
            $targetClass
        )->__invoke();
    }


    /**
     * @param mixed $targetClass
     * @covers ::enableRouteParameter
     * @covers ::disableRouteParameter
     * @depends test___construct
     */
    public function test_enableAndDisableRouteParameter($targetClass)
    {
        //テスト対象メソッドの実行
        \Closure::bind(
            function () use ($targetClass) {
                $actual = $targetClass->enableRouteParameter();
                $this->assertTrue($targetClass->enableRoutParameter);
                $this->assertInstanceOf(IdentityHandler::class, $actual);

                $actual = $targetClass->disableRouteParameter();
                $this->assertFalse($targetClass->enableRoutParameter);
                $this->assertInstanceOf(IdentityHandler::class, $actual);

                $actual = $targetClass->enableRouteParameter();
                $this->assertTrue($targetClass->enableRoutParameter);
                $this->assertInstanceOf(IdentityHandler::class, $actual);
            },
            $this,
            $targetClass
        )->__invoke();
    }

    /**
     * @param mixed $targetClass
     * @covers ::enableViewVariable
     * @covers ::disableViewVariable
     * @depends test___construct
     */
    public function test_enableAndDisableViewVariable($targetClass)
    {
        //テスト対象メソッドの実行
        \Closure::bind(
            function () use ($targetClass) {
                $actual = $targetClass->enableViewVariable();
                $this->assertTrue($targetClass->enableViewVariable);
                $this->assertInstanceOf(IdentityHandler::class, $actual);

                $actual = $targetClass->disableViewVariable();
                $this->assertFalse($targetClass->enableViewVariable);
                $this->assertInstanceOf(IdentityHandler::class, $actual);

                $actual = $targetClass->enableViewVariable();
                $this->assertTrue($targetClass->enableViewVariable);
                $this->assertInstanceOf(IdentityHandler::class, $actual);
            },
            $this,
            $targetClass
        )->__invoke();
    }

    /**
     * @param mixed $targetClass
     * @covers ::setIdentifier
     * @depends test___construct
     */
    public function test_setIdentifier($targetClass)
    {
        $actual = $targetClass->setIdentifier('string-identifier');
        $this->assertInstanceOf(IdentityHandler::class, $actual);
        //テスト対象メソッドの実行
        \Closure::bind(
            function () use ($targetClass) {
                //assertions
                $this->assertSame('string-identifier', $targetClass->identifier);
            },
            $this,
            $targetClass
        )->__invoke();
    }

    /**
     * @covers ::retrieveIdentity
     * @covers ::idInViewVariable
     * @covers ::idInInput
     * @covers ::idInRouteParameter
     * @dataProvider dataProviderPriority
     * @param $enableRoute
     * @param $enableInput
     * @param $enableView
     * @param $expects
     */
    public function test_retrieveIdentity_Priority($enableRoute, $enableInput, $enableView, $expects)
    {
        $request = \Mockery::mock(Request::class);
        $request->shouldReceive('route->parameter')->with('id')->andReturn('id-in-route-parameter');
        $request->shouldReceive('input')->with('id')->andReturn('id-in-input');

        $view = \Mockery::mock(View::class);
        $view->shouldReceive('getData')->andReturn(['id' => 'id-in-view-variable']);

        $targetClass = new $this->testClassName($request, $view);
        //設定1
        if ($enableRoute) {
            $targetClass->enableRouteParameter();
        } else {
            $targetClass->disableRouteParameter();
        }

        //設定2
        if ($enableInput) {
            $targetClass->enableInput();
        } else {
            $targetClass->disableInput();
        }

        //設定3
        if ($enableView) {
            $targetClass->enableViewVariable();
        } else {
            $targetClass->disableViewVariable();
        }

        $actual = $targetClass->retrieveIdentity();
        $this->assertSame($expects, $actual);
    }

    /**
     * @covers ::retrieveIdentity
     * @covers ::idInViewVariable
     * @covers ::idInInput
     * @covers ::idInRouteParameter
     * @dataProvider dataProviderPriority_WithNullView
     * @param $enableRoute
     * @param $enableInput
     * @param $enableView
     * @param $expects
     */
    public function test_retrieveIdentity_WithNullView($enableRoute, $enableInput, $enableView, $expects)
    {
        $request = \Mockery::mock(Request::class);
        $request->shouldReceive('route->parameter')->with('id')->andReturn('id-in-route-parameter');
        $request->shouldReceive('input')->with('id')->andReturn('id-in-input');

        $targetClass = new $this->testClassName($request);

        //設定1
        if ($enableRoute) {
            $targetClass->enableRouteParameter();
        } else {
            $targetClass->disableRouteParameter();
        }

        //設定2
        if ($enableInput) {
            $targetClass->enableInput();
        } else {
            $targetClass->disableInput();
        }

        //設定3
        if ($enableView) {
            $targetClass->enableViewVariable();
        } else {
            $targetClass->disableViewVariable();
        }

        $actual = $targetClass->retrieveIdentity();
        $this->assertSame($expects, $actual);
    }


    /**
     * @return array[]
     */
    public function dataProviderPriority()
    {
        return [
            //route , input , view , expects
            [true, true, true, 'id-in-route-parameter'],
            [true, true, false, 'id-in-route-parameter'],
            [true, false, true, 'id-in-route-parameter'],
            [true, false, false, 'id-in-route-parameter'],
            [false, true, true, 'id-in-input'],
            [false, true, false, 'id-in-input'],
            [false, false, true, 'id-in-view-variable'],
            [false, false, false, null],
        ];
    }

    /**
     * @return array[]
     */
    public function dataProviderPriority_WithNullView()
    {
        return [
            //route , input , view , expects
            [true, true, true, 'id-in-route-parameter'],
            [true, true, false, 'id-in-route-parameter'],
            [true, false, true, 'id-in-route-parameter'],
            [true, false, false, 'id-in-route-parameter'],
            [false, true, true, 'id-in-input'],
            [false, true, false, 'id-in-input'],
            [false, false, true, null], //Viewがnullの場合。このテストケースの動作のみNULLに変わる
            [false, false, false, null],
        ];
    }

}
