<?php

/**
 * Funzioni di aiuto per la semplificazione del codice.
 *
 * @since 2.4.2
 */
use HTMLBuilder\HTMLBuilder;

/**
 * Restituisce l'oggetto dedicato alla gestione della connessione con il database.
 *
 * @return \Database
 */
function database()
{
    return \App::getContainer()->get('database');
}

/**
 * Prepara il parametro inserito per l'inserimento in una query SQL.
 * Attenzione: protezione di base contro SQL Injection.
 *
 * @param string $parameter
 *
 * @since 2.3
 *
 * @return mixed
 */
function prepare($parameter)
{
    return database()->prepare($parameter);
}

/**
 * Restituisce il contenuto sanitarizzato dell'input dell'utente.
 *
 * @param string $param  Nome del parametro
 * @param string $method Posizione del parametro (post o get)
 * @param bool   $raw    Restituire il valore non formattato
 *
 * @since 2.3
 *
 * @return string
 */
function filter($param, $method = null, $parse = true)
{
    return container()->get('filter')->getValue($param, $method, $parse);
}

/**
 * Restituisce il contenuto sanitarizzato dell'input dell'utente.
 *
 * @param string $param Nome del parametro
 * @param bool   $raw   Restituire il valore non formattato
 *
 * @since 2.3
 *
 * @return string
 */
function post($param, $parse = true)
{
    return container()->get('filter')->getValue($param, 'post', $parse);
}

/**
 * Restituisce il contenuto sanitarizzato dell'input dell'utente.
 *
 * @param string $param Nome del parametro
 * @param bool   $raw   Restituire il valore non formattato
 *
 * @since 2.3
 *
 * @return string
 */
function get($param, $parse = true)
{
    return container()->get('filter')->getValue($param, 'get', $parse);
}

/**
 * Legge il valore di un'impostazione dalla tabella zz_settings.
 *
 * @param string $name
 * @param bool   $again
 *
 * @since 2.4.2
 *
 * @return string
 */
function setting($name, $again = false)
{
    return \Models\Setting::get($name)->valore;
}

/**
 * Restituisce l'oggetto dedicato alla gestione dei messaggi per l'utente.
 *
 * @since 2.4.2
 *
 * @return \Util\Messages
 */
function flash()
{
    return \App::getContainer()->get('flash');
}

/**
 * Restituisce l'oggetto dedicato alla gestione dell'autenticazione degli utente.
 *
 * @since 2.4.2
 *
 * @return \Auth
 */
function auth()
{
    return \App::getContainer()->get('auth');
}

/**
 * Restituisce l'oggetto dedicato alla gestione della traduzione del progetto.
 *
 * @since 2.4.2
 *
 * @return \Translator
 */
function trans()
{
    return \Translator::getInstance();
}

/**
 * Restituisce l'oggetto dedicato alla gestione della conversione di numeri e date.
 *
 * @since 2.4.2
 *
 * @return \Intl\Formatter
 */
function formatter()
{
    return \Translator::getFormatter();
}

/**
 * Restituisce la traduzione del messaggio inserito.
 *
 * @param string $string
 * @param array  $parameters
 * @param string $operations
 *
 * @since 2.3
 *
 * @return string
 */
function tr($string, $parameters = [], $operations = [])
{
    return \Translator::translate($string, $parameters, $operations);
}

// Retrocompatibilità (con la funzione gettext)
if (!function_exists('_')) {
    function _($string, $parameters = [], $operations = [])
    {
        return tr($string, $parameters, $operations);
    }
}

/**
 * Restituisce l'oggetto dedicato alla gestione dei log.
 *
 * @since 2.4.2
 *
 * @return \Monolog\Logger
 */
function logger()
{
    return Monolog\Registry::getInstance('logs');
}

/**
 * Restituisce il numero indicato formattato secondo la configurazione del sistema.
 *
 * @param float $number
 * @param int   $decimals
 *
 * @return string
 *
 * @since 2.4.8
 */
function numberFormat($number, $decimals = null)
{
    return Translator::numberToLocale($number, $decimals);
}

/**
 * Restituisce il timestamp indicato formattato secondo la configurazione del sistema.
 *
 * @param string $timestamp
+ *
 * @return string
 *
 * @since 2.4.8
 */
function timestampFormat($timestamp)
{
    return Translator::timestampToLocale($timestamp);
}

/**
 * Restituisce la data indicata formattato secondo la configurazione del sistema.
 *
 * @param string $date
 *
 * @return string
 *
 * @since 2.4.8
 */
function dateFormat($date)
{
    return Translator::dateToLocale($date);
}

/**
 * Restituisce l'orario indicato formattato secondo la configurazione del sistema.
 *
 * @param string $time
 *
 * @return string
 *
 * @since 2.4.8
 */
function timeFormat($time)
{
    return Translator::timeToLocale($time);
}

/**
 * Restituisce il simbolo della valuta del gestione.
 *
 * @since 2.4.9
 *
 * @return string
 */
function currency()
{
    return \Translator::getCurrency();
}

/**
 * Restituisce il numero indicato formattato come una valuta secondo la configurazione del sistema.
 *
 * @param string $time
 *
 * @return string
 *
 * @since 2.4.9
 */
function moneyFormat($number, $decimals = null)
{
    return tr('_TOTAL_ _CURRENCY_', [
        '_TOTAL_' => numberFormat($number, $decimals),
        '_CURRENCY_' => currency(),
    ]);
}

/**
 * Restituisce il numero indicato formattato come una valuta secondo la configurazione del sistema.
 *
 * @return string
 *
 * @since 2.4.11
 */
function input(array $json)
{
    return HTMLBuilder::parse($json);
}

/*
 * Restituisce il modulo relativo all'identificativo.
 *
 * @since 2.5
 *
 * @return \Modules\Module
 */
function module($identifier)
{
    return \Modules\Module::get($identifier);
}

function asset(string $name)
{
    return App::asset($name);
}

/**
 * Restituisce la distanza in tempo formattata per l'utente.
 *
 * @param string $timestamp
 *
 * @return string
 *
 * @since 2.5
 */
function diffForHumans($timestamp)
{
    return \Carbon\Carbon::parse($timestamp)->diffForHumans();
}

/**
 * Restituisce il percorso per la risorsa $name.
 *
 * @param string $name
 * @param array  $parameters
 *
 * @return string
 *
 * @since 2.5
 */
function urlFor($name, $parameters = [])
{
    $router = container()->get('router');

    if (strpos($name, 'module') == 0) {
        $name = str_replace('module', $parameters['module_id'].'-module', $name);
    }

    return $router->urlFor($name, $parameters);
}

/**
 * Restituisce il contenitore Slim per DI.
 *
 * @return \DI\Container
 *
 * @since 2.5
 */
function container()
{
    return App::getContainer();
}
