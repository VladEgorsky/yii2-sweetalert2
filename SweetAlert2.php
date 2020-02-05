<?php

namespace vlad2112\sweetalert2;

use Yii;
use yii\helpers\ArrayHelper;
use yii\helpers\Json;
use yii\web\JsExpression;

/**
 *
 *
 *
 * https://sweetalert2.github.io/
 * Расшифровка для ключей (defaultConfig) - https://sweetalert2.github.io/#configuration
 * Примеры использования - https://sweetalert2.github.io/#examples
 *
 * В контроллерах использовать код :
 * Yii::$app->session->setFlash(uniqid(SweetAlert2::FLASH_PREFIX), [
 *      'title' => 'Title',
 *      'titleText' => 'Ooooops ...',
 *      'icon' => 'error',
 *     'showClass' => [
 *          'popup' => 'swal2-show'
 *      ],
 *      'hideClass' => [
 *          'popup' => 'swal2-hide'
 *      ],
 *      'html' =>
 *          'You can use <b>bold text</b>, ' .
 *          '<a href="//sweetalert2.github.io">links</a> ' .
 *          'and other HTML tags',
 *      'footer' => '<a href>Why do I have this issue?</a>',
 * ]);
 */
class SweetAlert2 extends \yii\bootstrap4\Widget
{
    const FLASH_PREFIX = 'alert2_';

    /**
     * @var array
     */
    protected $flashMessages = [];

    /**
     * @var array
     */
    protected $defaultConfig = [
        'title' => '',
        'titleText' => '',
        'html' => '',
        'text' => '',
        'icon' => false,
        'iconHtml' => false,

        // 'animation' => false,        // Deprecated, use showClass and hideClass instead.
        'showClass' => [
            'popup' => 'animated fadeInDown fast',        // 'swal2-show',
            'backdrop' => 'swal2-backdrop-show',
            'icon' => 'swal2-icon-show',
        ],
        'hideClass' => [
            'popup' => 'animated fadeOutUp faster',        // 'swal2-hide',
            'backdrop' => 'swal2-backdrop-hide',
            'icon' => 'swal2-icon-hide',
        ],

        'footer' => '',
        'backdrop' => true,
        'toast' => false,
        'target' => 'body',

        // can be text, email, password, number, tel, range,
        // textarea, select, radio, checkbox, file and url
        'input' => false,

        'width' => false,
        'padding' => '1.25rem',
        'background' => '#fff',
        'position' => 'center',

        // can be set to 'row', 'column', 'fullscreen', or false
        'grow' => false,

        /**
         * customClass: {
         * container: 'container-class',
         * popup: 'popup-class',
         * header: 'header-class',
         * title: 'title-class',
         * closeButton: 'close-button-class',
         * icon: 'icon-class',
         * image: 'image-class',
         * content: 'content-class',
         * input: 'input-class',
         * actions: 'actions-class',
         * confirmButton: 'confirm-button-class',
         * cancelButton: 'cancel-button-class',
         * footer: 'footer-class'
         * } */
        'customClass' => [],

        // Auto close timer of the modal. Set in ms
        'timer' => false,
        'timerProgressBar' => false,

        'heightAuto' => true,
        'allowOutsideClick' => true,
        'allowEscapeKey' => true,
        'allowEnterKey' => true,
        'stopKeydownPropagation' => true,
        'keydownListenerCapture' => false,

        'showConfirmButton' => true,
        'showCancelButton' => false,
        'confirmButtonText' => 'OK',
        'cancelButtonText' => 'Cancel',
        'confirmButtonColor' => '#3085d6',
        'cancelButtonColor' => '#aaa',
        'confirmButtonAriaLabel' => '',
        'cancelButtonAriaLabel' => '',
        'buttonsStyling' => true,
        'reverseButtons' => false,
        'focusConfirm' => true,
        'focusCancel' => false,
        'showCloseButton' => false,
        'closeButtonHtml' => '&times;',
        'closeButtonAriaLabel' => 'Close this dialog',
        'showLoaderOnConfirm' => false,
        'scrollbarPadding' => true,

        'preConfirm' => false,      // Function to execute before confirm

        'imageUrl' => '',
        'imageWidth' => '',
        'imageHeight' => '',
        'imageAlt' => '',

        'inputPlaceholder' => '',
        'inputValue' => '',         // Input field initial value
        'inputOptions' => [],
        'inputAutoTrim' => true,
        'inputAttributes' => [],
        'inputValidator' => false,
        'validationMessage' => false,
        'progressSteps' => [],
        'currentProgressStep' => false,
        'progressStepsDistance' => '40px',

        'onBeforeOpen' => false,        // Function to run when modal built
        'onOpen' => false,
        'onRender' => false,
        'onClose' => false,
        'onAfterClose' => false,
        'onDestroy' => false,
    ];

    /**
     * @return bool|void
     * @throws \yii\base\InvalidConfigException
     */
    public function init()
    {
        $session = Yii::$app->session;
        $flashes = $session->getAllFlashes();

        // Из всех флеш-данных выбираем только те, которые начнияются на self::FLASH_PREFIX
        $this->flashMessages = array_filter($flashes, function ($v, $k) use ($session) {
            if (substr($k, 0, strlen(self::FLASH_PREFIX)) == self::FLASH_PREFIX) {
                $session->removeFlash($k);
                return $v;
            }
        }, ARRAY_FILTER_USE_BOTH);

        if (empty($this->flashMessages)) {
            return false;
        }
    }


    /**
     * {@inheritdoc}
     */
    public function run()
    {
        $messArray = [];
        foreach ($this->flashMessages as $userConfig) {
            $messArray[] = ArrayHelper::merge($this->defaultConfig, $userConfig);
        }

        if (!empty($messArray)) {
            $messJs = new JsExpression(Json::encode($messArray));
            $jsCode = "Swal.queue($messJs);";
            Yii::$app->view->registerJs($jsCode);
        }
    }
}
