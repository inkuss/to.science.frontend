Facetten im Sourcecode anpassen
===============================

Für die Anpassung der Facetten im Quellcode sind besonders diese Dateien relevant 

* edoweb_storage/EdowebAPIClient.inc
* edoweb/php/moduleSettings.php 
* edoweb/js/edoweb_view.js


In der ersten Datei werden die Anfragen an an den Elasticsearch-Index generiert, die die für die Facetten benötigten Daten verwenden. Die derzeit verwendete 
Elasticsearch 1.1 kennt für die Abfrage von Facetten ein mit "facets" bezeichnetes Array, mit dem die entsprechende Abfrage generiert wird. In späteren Elasicsearch-Versionen 
gibt es dieses Atrray nicht mehr. Im Unterschied zu dem "query" genannten Array funktioniert hier die Sortierung der Ergebnisliste deutlich anders.

query kennt ein inneres Array namens "sort", dass als weiteres Element "order" enthält. "sort enthält die Information welches Feld sortiert werden soll, "order" kann z.B. "desc" oder "asc" sein. 

facet kennt nur "order" mit den Begriffen "count" [default], "term" und "reverse_term". Während count nach der Anzahl der treffer sortiert, sortieren term und reverse_term auf- und absteigend
nach dem Feldnamen.

https://www.elastic.co/guide/en/elasticsearch/reference/0.90/search-facets-terms-facet.html

gibt eine genaue Übersicht.

In der zweiten Datei habe ich Formulare zur Konfiguration der Facetten angelegt, bzw. überarbeitet. Damit wird eine rollenspezifische Auswahl der Facetten ermöglicht. 
Das ist kein Muss, mit der Konfigurierbarkeit wird jedoch eine deutlich vereinfachte Wartung der Facetten erreicht, die nach meiner Erfahrung immer wieder angepasst werden müssen.

In der dritten Datei gibt es die Möglichkeit, die Facetten auf Basis einer jquery-Funktionalität zu sortieren. Dadurch kann eine bereits getroffene Auswahl der Facetten nach den 
relevantesten (die meisten Treffer) anschließend nach einem anderen Kriterium sortiert angezeigt werden. Mach jedoch in meinem Beispiel wenig Sinn.

Kontrolle der Ergebnisse einer Elasticsearch-Abfrage
====================================================

Die Elasticsearch ist aus Sicherheitsgründen nur von localhost und ausgewählten IP-Adressen innerhalb des hbz erreichbar. Die erlaubten IP-Adressen sind in der Apache-Config "site.cfg" 
eingetragen. Der Apache2-Server nutzt die site.cfg aus dem Verzeichnis /opt/regal/prod-conf, die über einen SymLink verknüpft ist. 
