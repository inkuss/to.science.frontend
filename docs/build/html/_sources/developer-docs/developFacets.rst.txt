Facetten im Sourcecode anpassen
===============================

Für die Anpassung der Facetten im Quellcode kommen besonders die Dateien 

* edoweb_storage/EdowebAPIClient.inc
* edoweb/php/moduleSettings.php 
* edoweb/js/edoweb_view.js

infrage. 

In der ersten Datei werden die Anfragen an an den Elasticsearch-Index generiert, die die für die Facetten benötigten Daten verwenden. Die derzeit verwendete 
Elasticsearch 1.1 kennt für die Abfrage von Facetten ein mit "facets" bezeichnetes Array, mit dem die entsprechende Abfrage generiert wird. In späteren Elasicsearch-Versionen 
gibt es dieses Atrray nicht mehr. Im Unterschied zu dem "query" genannten Array funktioniert hier die Sortierung der Ergebnisliste deutlich anders.

query kennt ein inneres Array namens "sort", dass als weiteres Element "order" enthält. "sort enthält die Information welches Feld sortiert werden soll, "order" kann z.B. "desc" oder "asc" sein. 

facet kennt nur "order" mit den Begriffen "count" [default], "term" und "reverse_term". Während count nach der Anzahl der treffer sortiert, sortieren term und reverse_term auf- und absteigend
nach dem Feldnamen.

https://www.elastic.co/guide/en/elasticsearch/reference/0.90/search-facets-terms-facet.html

gibt eine genaue Übersicht.

Hier habe ich z.B. die Formulare angelegt, die eine rollenspezifische Auswahl der Facetten ermöglichen. In der Datei EdowebAPIClient.inc erfolgt die rollenspezifische Vorbereitung 
der Facetten. 
