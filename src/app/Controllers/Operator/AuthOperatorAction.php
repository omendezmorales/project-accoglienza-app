<?php
/**
 * Copyright (c) 2017. This file belongs to Misericordia di "Torre del lago Puccini"
 */

namespace App\Controllers\Operator;

use App\Auth\Auth;
use App\Validation\Validator;
use Psr\Http\Message\ResponseInterface;
use Respect\Validation\Validator as v;
use Slim\Flash\Messages;
use Slim\Http\Request;
use Slim\Http\Response;
use Slim\Interfaces\RouterInterface;

/**
 * Class AuthOperatorAction
 * @package App\Controllers\Operator
 * @author Javier Mellado <sol@javiermellado.com>
 */
final class AuthOperatorAction
{
    /**
     * @var Auth
     */
    protected $auth;
    /**
     * @var Validator
     */
    protected $validator;
    /**
     * @var RouterInterface
     */
    protected $router;

    /** @var Messages */
    protected $flash;

    /**
     * OperatorController constructor.
     * @param RouterInterface $router
     * @param Auth $auth
     * @param Validator $validator
     * @param Messages $flash
     */
    function __construct(
        RouterInterface $router,
        Auth $auth,
        Validator $validator,
        Messages $flash
    )
    {
        $this->auth = $auth;
        $this->router = $router;
        $this->validator = $validator;
        $this->flash = $flash;
    }

    /**
     * @param Request $request
     * @param Response $response
     * @return \Psr\Http\Message\ResponseInterface
     */
    public function __invoke(Request $request, Response $response): ResponseInterface
    {
        $validation = $this->validator->validate($request, [
            'email' => v::noWhitespace()->notEmpty()->emailValid(),
            'password' => v::noWhitespace()->notEmpty()->passwordValid($request->getParam('email'), $this->auth),
        ]);

        if ($validation->failed()) {
            $this->flash->addMessage('error', 'Login failed. Wrong Credentials');
            return $response->withRedirect($this->router->pathFor('home'));
        }

        return $response->withRedirect($this->router->pathFor('home-loggedin'));
    }
}