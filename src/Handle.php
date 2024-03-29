<?php declare (strict_types = 1);
#coding: utf-8
# +-------------------------------------------------------------------
# | 系统异常处理类
# +-------------------------------------------------------------------
# | Copyright (c) 2017-2019 Sower rights reserved.
# +-------------------------------------------------------------------
# +-------------------------------------------------------------------
namespace sower\exception;

use Exception;
use sower\App;
use sower\console\Output;
use sower\service\database\exception\DataNotFoundException;
use sower\service\database\exception\ModelNotFoundException;
use sower\Request;
use sower\Response;
use sower\response\Json;
use Throwable;
class Handle
{
    /** @var App */
    protected $app;

    protected $ignoreReport = [
        HttpException::class,
        HttpResponseException::class,
        ModelNotFoundException::class,
        DataNotFoundException::class,
        ValidateException::class,
    ];

    protected $isJson = false;

    public function __construct(App $app)
    {
        $this->app = $app;
    }

    /**
     * Report or log an exception.
     *
     * @access public
     * @param  Throwable $exception
     * @return void
     */
    public function report(Throwable $exception): void
    {
        if (!$this->isIgnoreReport($exception)) {
            // 收集异常数据
            if ($this->app->isDebug()) {
                $data = [
                    'file'    => $exception->getFile(),
                    'line'    => $exception->getLine(),
                    'message' => $this->getMessage($exception),
                    'code'    => $this->getCode($exception),
                ];
                $log = "[{$data['code']}]{$data['message']}[{$data['file']}:{$data['line']}]";
            } else {
                $data = [
                    'code'    => $this->getCode($exception),
                    'message' => $this->getMessage($exception),
                ];
                $log = "[{$data['code']}]{$data['message']}";
            }

            if ($this->app->config->get('log.record_trace')) {
                $log .= PHP_EOL . $exception->getTraceAsString();
            }

            $this->app->log->record($log, 'error');
        }
    }

    protected function isIgnoreReport(Throwable $exception): bool
    {
        foreach ($this->ignoreReport as $class) {
            if ($exception instanceof $class) {
                return true;
            }
        }

        return false;
    }

    /**
     * Render an exception into an HTTP response.
     *
     * @access public
     * @param Request   $request
     * @param Throwable $e
     * @return Response
     */
    public function render($request, Throwable $e): Response
    {
        $this->isJson = $request->isJson();
        if ($e instanceof HttpException) {
            return $this->renderHttpException($e);
        } else {
            return $this->convertExceptionToResponse($e);
        }
    }

    /**
     * @access public
     * @param  Output    $output
     * @param  Throwable $e
     */
    public function renderForConsole(Output $output, Throwable $e): void
    {
        if ($this->app->isDebug()) {
            $output->setVerbosity(Output::VERBOSITY_DEBUG);
        }

        $output->renderException($e);
    }

    /**
     * @access protected
     * @param  HttpException $e
     * @return Response
     */
    protected function renderHttpException(HttpException $e): Response
    {
        $status   = $e->getStatusCode();
        $template = $this->app->config->get('app.http_exception_template');

        if (!$this->app->isDebug() && !empty($template[$status])) {
            return Response::create($template[$status], 'view', $status)->assign(['e' => $e]);
        } else {
            return $this->convertExceptionToResponse($e);
        }
    }

    /**
     * 收集异常数据
     * @param Throwable $exception
     * @return array
     */
    protected function convertExceptionToArray(Throwable $exception): array
    {
        if ($this->app->isDebug()) {
            // 调试模式，获取详细的错误信息
            $data = [
                'name'    => get_class($exception),
                'file'    => $exception->getFile(),
                'line'    => $exception->getLine(),
                'message' => $this->getMessage($exception),
                'trace'   => $exception->getTrace(),
                'code'    => $this->getCode($exception),
                'source'  => $this->getSourceCode($exception),
                'datas'   => $this->getExtendData($exception),
                'tables'  => [
                    'GET Data'              => $_GET,
                    'POST Data'             => $_POST,
                    'Files'                 => $_FILES,
                    'Cookies'               => $_COOKIE,
                    'Session'               => $_SESSION ?? [],
                    'Server/Request Data'   => $_SERVER,
                    'Environment Variables' => $_ENV,
                    'Sower Constants'    => $this->getConst(),
                ],
            ];
        } else {
            // 部署模式仅显示 Code 和 Message
            $data = [
                'code'    => $this->getCode($exception),
                'message' => $this->getMessage($exception),
            ];

            if (!$this->app->config->get('app.show_error_msg')) {
                // 不显示详细错误信息
                $data['message'] = $this->app->config->get('app.error_message');
            }
        }

        return $data;
    }

    /**
     * @access protected
     * @param  Throwable $exception
     * @return Response
     */
    protected function convertExceptionToResponse(Throwable $exception): Response
    {
        $data = $this->convertExceptionToArray($exception);

        if (!$this->isJson) {
            //保留一层
            while (ob_get_level() > 1) {
                ob_end_clean();
            }

            $data['echo'] = ob_get_clean();

            ob_start();
            extract($data);
            include $this->app->config->get('app.exception_tmpl') ?: __DIR__ . '/../../html/exception.html';

            // 获取并清空缓存
            $data     = ob_get_clean();
            $response = new Response($data);
        } else {
            $response = new Json($data);
        }

        if ($exception instanceof HttpException) {
            $statusCode = $exception->getStatusCode();
            $response->header($exception->getHeaders());
        }

        return $response->code($statusCode ?? 500);
    }

    /**
     * 获取错误编码
     * ErrorException则使用错误级别作为错误编码
     * @access protected
     * @param  Throwable $exception
     * @return integer                错误编码
     */
    protected function getCode(Throwable $exception)
    {
        $code = $exception->getCode();

        if (!$code && $exception instanceof ErrorException) {
            $code = $exception->getSeverity();
        }

        return $code;
    }

    /**
     * 获取错误信息
     * ErrorException则使用错误级别作为错误编码
     * @access protected
     * @param  Throwable $exception
     * @return string                错误信息
     */
    protected function getMessage(Throwable $exception): string
    {
        $message = $exception->getMessage();

        if ($this->app->runningInConsole()) {
            return $message;
        }

        $lang = $this->app->lang;

        if (strpos($message, ':')) {
            $name    = strstr($message, ':', true);
            $message = $lang->has($name) ? $lang->get($name) . strstr($message, ':') : $message;
        } elseif (strpos($message, ',')) {
            $name    = strstr($message, ',', true);
            $message = $lang->has($name) ? $lang->get($name) . ':' . substr(strstr($message, ','), 1) : $message;
        } elseif ($lang->has($message)) {
            $message = $lang->get($message);
        }

        return $message;
    }

    /**
     * 获取出错文件内容
     * 获取错误的前9行和后9行
     * @access protected
     * @param  Throwable $exception
     * @return array                 错误文件内容
     */
    protected function getSourceCode(Throwable $exception): array
    {
        // 读取前9行和后9行
        $line  = $exception->getLine();
        $first = ($line - 9 > 0) ? $line - 9 : 1;

        try {
            $contents = file($exception->getFile()) ?: [];
            $source   = [
                'first'  => $first,
                'source' => array_slice($contents, $first - 1, 19),
            ];
        } catch (Exception $e) {
            $source = [];
        }

        return $source;
    }

    /**
     * 获取异常扩展信息
     * 用于非调试模式html返回类型显示
     * @access protected
     * @param  Throwable $exception
     * @return array                 异常类定义的扩展数据
     */
    protected function getExtendData(Throwable $exception): array
    {
        $data = [];

        if ($exception instanceof \sower\Exception) {
            $data = $exception->getData();
        }

        return $data;
    }

    /**
     * 获取常量列表
     * @access private
     * @return array 常量列表
     */
    private static function getConst(): array
    {
        $const = get_defined_constants(true);

        return $const['user'] ?? [];
    }
}
