<?php

namespace app\assets;

use yii\web\AssetBundle;

class NpmAsset extends AssetBundle
{
    public $sourcePath = '@app/node_modules';
    public $js = [
        'aos/dist/aos.js',
        'axios/dist/axios.min.js',
        'jspdf/dist/jspdf.umd.min.js',
        'html2canvas/dist/html2canvas.min.js',
        'https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js',
        'quagga/dist/quagga.min.js',
        'sweetalert2/dist/sweetalert2.min.js',
    ];
    public $css = [
        'bootstrap-icons/font/bootstrap-icons.min.css',
        'aos/dist/aos.css',
        'animate.css/animate.min.css',
        'sweetalert2/dist/sweetalert2.min.css',
    ];
}