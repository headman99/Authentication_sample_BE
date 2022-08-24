<?php
/**
 * Created by PhpStorm.
 * User: dmormile
 * Date: 19/03/2019
 * Time: 12:08
 */

use Illuminate\Support\Facades\Log;

if (!function_exists('getPathPublic')) {
    /**
     * Return relative public path.
     *
     *
     * @return string with path app/public/
     */
    function getPathPublic()
    {
        return "app/public/";
    }

}

if (!function_exists('getPathDocumenti')) {
    /**
     * Return relative public path documenti.
     *
     *
     * @return string with path documenti/
     */
    function getPathDocumenti()
    {
        return 'documenti/';
    }
}

if (!function_exists('getPathDatabaseForImport')) {
    /**
     * Return relative public path.
     *
     *
     * @return string with path app/public/
     */
    function getPathDatabaseForImport()
    {
        return database_path("importfiles");
    }

}

if (!function_exists('getFullPathStorage')) {
    /**
     * Return absolute public path.
     * @param String $relativepath
     *
     * @return string with path c:/webapp/gfs/storage/pathrelative
     */
    function getFullPathStorage($relativepath)
    {
        return storage_path($relativepath);
    }
}

if (!function_exists('getPathDocumenti')) {
    /**
     * Return relative public path documenti for azienda.
     * @param  string $codicesocieta
     * @param  string $codiceazienda
     * @param  string $percorso
     *
     * @return string with path codicesocieta/codiceazienda/perscorso
     */
    function getPathDocumenti($codiceazienda, $percorso = null)
    {
        $path = $codiceazienda . '/';
        if (!empty($percorso)) {
            $path .= $percorso;
        }
        return $path;
    }
}

if (!function_exists('getPathTemplate')) {
    /**
     * Return relative public path template.
     * @param string $categoria
     *
     * @return string with path template/categoria/
     */
    function getPathTemplate($categoria)
    {
        return 'template/' . $categoria . '/';
    }
}


if (!function_exists('getPathFatturePassive')) {
    /**
     * Return relative public path fatture passive.
     *
     *
     * @return string with path fatturapassiva/documenti/
     */
    function getPathFatturePassive()
    {
        return 'fatturapassiva/documenti/';
    }
}

if (!function_exists('listFolderFiles')) {
    function listFolderFiles($dir, $first)
    {
        $fileFolderList = scandir($dir);
        if ($first) {
            $result = '<ul id="treeview">';
        } else {
            $result = '<ul>';
        }
        foreach ($fileFolderList as $fileFolder) {
            if ($fileFolder != '.' && $fileFolder != '..') {
                if (!is_dir($dir . '/' . $fileFolder)) {
                    $path = 'storage/app/public/' . str_replace(storage_path('app/public/'), '', $dir);
                    $result .= '<li data-icon-cls="fa fa-file"><a target="_blank" href="../../' . ltrim($path . '/' . $fileFolder, './') . '">' . $fileFolder . '</a>';
                } else {
                    $result .= '<li data-icon-cls="fa fa-folder">' . $fileFolder;
                }
                if (is_dir($dir . '/' . $fileFolder)) $result .= listFolderFiles($dir . '/' . $fileFolder, false);
                $result .= '</li>';
            }
        }
        return $result .= '</ul>';
    }
}

if (!function_exists('sendMail')) {
    /**
     * Send mail
     * @param $codicesocieta
     * @param $to
     * @param $subject
     * @param $template blade template
     * @param $dataTemplate data to put in blade template
     * @param null $pathallegato
     */
    function sendMail($denominazione, $to, $subject, $template, $dataTemplate, $pathallegato = null)
    {

        $data = array_merge($dataTemplate, array('denominazione' => $denominazione));

        \Illuminate\Support\Facades\Mail::send($template, $data, function ($message) use ($pathallegato, $to, $subject) {
            $message->to($to, $to)->cc(\Illuminate\Support\Facades\Config::get('mail.username'), \Illuminate\Support\Facades\Config::get('mail.username'))->subject($subject);
            if (!empty($pathallegato)) {
                $message->attach($pathallegato);
            }
            $message->from(\Illuminate\Support\Facades\Config::get('mail.username'), \Illuminate\Support\Facades\Config::get('mail.username'));
        });
    }
}

if (!function_exists('adaptInputToModel')) {
    function adaptInputToModel(\Illuminate\Http\Request $request, \Illuminate\Database\Eloquent\Model $model)
    {
        return array_filter($request->only($model->getFillable()));
    }
}

if (!function_exists('time_elapsed_string')) {
    function time_elapsed_string($ptime) {
        $ptime = strtotime($ptime);
        $etime = time() - $ptime;
        if ($etime < 1) return '0 seconds';

        $a = array(
            12 * 30 * 24 * 60 * 60 =>  array('anno','anni'),
            30 * 24 * 60 * 60 =>  array('mese','mesi'),
            24 * 60 * 60 => array('giorno','giorni'),
            60 * 60 => array('ora','ore'),
            60 => array('minuto','minuti'),
            1 => array('secondo','secondi')
        );

        foreach ($a as $secs => $str) {
            $d = $etime / $secs;
            if ($d >= 1) {
                $r = round($d);
                return $r . ' ' . ($r > 1 ? $str[1] : $str[0]) . ' fa';
            }
        }
    }
}

if (!function_exists('randomByMicrotime')) {
    function randomByMicrotime($limit)
    {
        return substr(md5(microtime()), 0, $limit);
    }
}

if (!function_exists('randomByMtRand')) {
    function randomByMtRand($limit)
    {
        return substr(base_convert(sha1(uniqid(mt_rand())), 16, 36), 0, $limit);
    }
}

if (!function_exists('getErrorException')) {
    function getErrorException($exc)
    {
        if(empty($exc))
        {
            return "Non è possibile catturare il tipo di errore";
        }
        if(!empty($exc->errorInfo))
        {
            $msg = implode(" ", $exc->errorInfo);
            Log::error('Dettaglio errore tracciato -> ' . $msg);
            if(env('APP_DEBUG') == false)
            {
                $msg = "Errore generico";
            }
            return $msg;
        }
        else if(empty($exc->errorInfo))
        {
            return $exc->getMessage();
        }
        return "Errore generico";
    }
}

