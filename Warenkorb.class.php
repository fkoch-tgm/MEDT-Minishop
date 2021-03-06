<?php
require_once 'articleTools.php';

/**
 * Class Warenkorb Ein Warenkorb der die einkäufe des Benutzers speichert
 * der Inhalt des Warenkorbs wird in der Session gespeichert
 */
class Warenkorb {
    private $wk;

    /**
     * Warenkorb constructor erzeugt einen Warenkorb aus der Session
     */
    function __construct()
    {
        if(! isset($_SESSION['warenkorb'])) {
            $this->wk = array();
        }
        else {
            $this->wk = unserialize($_SESSION['warenkorb']);
        }
    }

    /**
     * Fügt einen neuen Artikel hinzu
     * @param $article string Eine Artikel-ID
     * @param $menge integer die Menge
     */
    function add($article,$menge) {
        if($menge <= 0) return;
        $articles=loadArticles();
        if(! isset($articles[$article])) return;
        $this->wk[$article] += $menge;
    }

    /**
     * Entfernt einen Artikel aus dem Warenkorb
     * @param $article string Eine Artikel-ID
     */
    function remove($article) {
        unset($this->wk[$article]);
    }

    function genHTML() {
        $articles = loadArticles();
        $gesamtpreis = 0;
        $html = '
<div class="row">
    <div class="col-md-9">
        <div class="table-responsive-sm">
            <table class="table table-hover table-dark table-striped" style="">
                <thead>
                <tr>
                    <th scope="col">Bezeichnung</th>
                    <th scope="col">Menge</th>
                    <th scope="col">Preis</th>
                    <th scope="col"></th>
                </tr>
                </thead>
                <tbody>
                ';
        foreach ($this->wk as $id => $menge) {
            if(! isset($articles[$id])) continue;
            $preis = $articles[$id]['preis'] * $menge;
            $html .= '
                <tr class="click-row" data-artikel-id="'.$id.'">
                    <th scope="row">
                        '.$id.'
                    </th>
                    <td>
                        '.$menge.' x '.$articles[$id]['gewicht'].' '.$articles[$id]['einheit'].'
                    </td>
                    <td>
                        '.$menge.' x '.$articles[$id]['preis'].'€  = '.$preis.' €
                    </td>
                    <td>
                        <a class="btn btn-sm btn-danger un-order-article">
                            <span class="oi oi-minus" title="Delete" aria-hidden="true"></span>
                        </a>
                    </td>
                </tr>';
            $gesamtpreis += $preis;
        }

        $html .= '
                </tbody>
            </table>
        </div>
    </div>
    <div class="col-md-3 bg-secondary  pt-2 pb-4 rounded">
        <div class="form-group">
            <label for="gesamtpreis">Gesamtpreis</label>
            <input class="form-control text-center" type="text" id="Gesamtpreis" contenteditable="false" disabled value="'.$gesamtpreis.' €">
        </div>
        <a href="index.php?site=buy" class="btn btn-primary w-100">Kaufen</a>
    </div>
</div>
    ';
        return $html;
    }
/**
     * @return int die Menge der Artikel
     */
    public function getTotalArticles() {
        $total = 0;
        foreach ($this->wk as $value) $total += $value;
        return $total;
    }

    /**
     * @return float|int gesamtkosten des Warenkorbs
     */
    public function getTotalCost() {
        $total = 0;
        $articles = loadArticles();
        foreach ($this->wk as $id=>$menge) $total += $articles[$id]['preis']*$menge;
        return $total;
    }

    /**
     * speichert die Änderungen vom Warenkorb in der Session
     */
    public function __destruct()
    {
        $_SESSION['warenkorb'] = serialize($this->wk);
    }

    /**
     * @return array das array ArtikelID => Menge
     */
    public function getArray() {
        return $this->wk;
    }

    /**
     * @param $id die id des Artikel
     * @return mixed Die Menge des Artikel im Warenkorb
     */
    public function getNumbersArticle($id){
        return $this->wk[$id];
    }
}
