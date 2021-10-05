<?php

namespace GitBalocco\LaravelUiUtils\Http;

use GitBalocco\LaravelUiUtils\Http\Contract\IdentityHandler as IdentityHandlerInterface;
use Illuminate\Http\Request;
use Illuminate\View\View;

/**
 * Class IdentityHandler
 * RequestとViewから操作対象であるデータの識別子を検出する。
 * @package GitBalocco\LaravelPresentations\Http\View
 */
class IdentityHandler implements IdentityHandlerInterface
{
    /** @var string $identifier 検出対象となる識別子名。デフォルトは "id" */
    private $identifier = 'id';

    /** @var bool $enableInput ルートパラメータ $request->route()->parameter() に対する検査を有効にするか */
    private $enableRoutParameter = true;

    /** @var bool $enableInput リクエストの入力 $request->input() に対する検査を有効にするか */
    private $enableInput = false;

    /** @var bool $enableViewVariable Viewにアサインされいてる変数 $view->getData() に対する検査を有効にするか */
    private $enableViewVariable = true;

    /** @var Request $request */
    private $request;

    /** @var View|null $view */
    private $view;

    /**
     * IdentityHandler constructor.
     * @param Request $request
     * @param View|null $view
     */
    public function __construct(Request $request, View $view = null)
    {
        $this->request = $request;
        $this->view = $view;
    }

    /**
     * retrieveId
     * 識別子の取得。優先順は、
     * 1.ルートパラメータ
     * 2.リクエスト入力
     * 3.Viewにアサインされた変数
     * の順。
     * 仮に上記3つに異なる値が指定された状態である場合、優先順に先に検出したものが採用される。
     *
     * @return mixed|object|string|null
     */
    public function retrieveIdentity()
    {
        return ($this->idInRouteParameter() ?? $this->idInInput() ?? $this->idInViewVariable());
    }

    /**
     * ルートパラメータに 'id' が存在していれば取得する。
     * @return object|string|null
     */
    private function idInRouteParameter()
    {
        if (!$this->enableRoutParameter) {
            return null;
        }
        return optional($this->request->route())->parameter($this->identifier);
    }

    /**
     * リクエストの入力値に 'id' が存在していれば取得する。
     * @return mixed|null
     */
    private function idInInput()
    {
        if (!$this->enableInput) {
            return null;
        }
        return $this->request->input($this->identifier);
    }

    /**
     * コントローラーからアサインされたview変数の中に 'id' が存在していれば取得する。
     * @return mixed|null
     */
    private function idInViewVariable()
    {
        if (!$this->enableViewVariable) {
            return null;
        }
        if (!$this->view) {
            return null;
        }
        $viewVariable = $this->view->getData();
        return ($viewVariable[$this->identifier] ?? null);
    }

    /**
     * ルートパラメータへの検査を有効化する
     * @return IdentityHandler
     */
    public function enableRouteParameter(): IdentityHandler
    {
        $this->enableRoutParameter = true;
        return $this;
    }

    /**
     * ルートパラメータへの検査を無効化する
     * @return IdentityHandler
     */
    public function disableRouteParameter(): IdentityHandler
    {
        $this->enableRoutParameter = false;
        return $this;
    }

    /**
     * リクエスト入力に対する検査を有効化する
     * @return IdentityHandler
     */
    public function enableInput(): IdentityHandler
    {
        $this->enableInput = true;
        return $this;
    }

    /**
     * リクエスト入力に対する検査を無効化する
     * @return IdentityHandler
     */
    public function disableInput(): IdentityHandler
    {
        $this->enableInput = false;
        return $this;
    }

    /**
     * Viewにアサインされている変数に対する検査を有効化する
     * @return IdentityHandler
     */
    public function enableViewVariable(): IdentityHandler
    {
        $this->enableViewVariable = true;
        return $this;
    }

    /**
     * Viewにアサインされている変数に対する検査を無効化する
     * @return IdentityHandler
     */
    public function disableViewVariable(): IdentityHandler
    {
        $this->enableViewVariable = false;
        return $this;
    }

    /**
     * 検査対象の名前を設定する。このメソッドで指定した名前で、
     * ルートパラメータ、リクエスト入力、Viewにアサインされた変数内を検査する。
     * @param string $identifier
     * @return IdentityHandler
     */
    public function setIdentifier(string $identifier): IdentityHandler
    {
        $this->identifier = $identifier;
        return $this;
    }
}
