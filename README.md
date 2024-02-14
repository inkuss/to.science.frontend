# About

to.science.drupal is a collection of Drupal 7 modules that provide a front end for [toscience](https://github.com/hbz/to.science) (repository and
graph-based api for library data).

# Installation

to.science.drupal depends on the redland bindings and curl modules for php5.
Installation on SLES SP4:

libraptor2-0 und raptor mit YaST2 installieren.

zypper addrepo https://download.opensuse.org/repositories/X11:common:Factory/SLE_15_SP2/X11:common:Factory.repo
zypper refresh
Dann librasqal3 (RDF Parser Toolkit for Redland), librdf0, rasqal und redland mit YaST2 installieren.

Install redland bindings on SLES for php56:
Alle Befehle werden als Root oder mit sudo ausgeführt.

    Zuerst notwendige Pakete installieren
    zypper in -t pattern devel_basis
    zypper in libredland-devel raptor re2c gdb valgrind swig libxml2-devel sqlite3-devel php56-devel librasqal-devel

    Redland-Bindings runterladen, und Makefile im php Unterordner austauschen
    # download redland bindings
    wget wget https://download.librdf.org/source/redland-bindings-1.0.17.1.tar.gz
    tar xf redland-bindings-1.0.17.1.tar.gz
    cd redland-bindings-1.0.17.1/
    ./autogen.sh --with-php=php56
    cd php/
     
    >> Makefile durch Inhalt von "makefile_changed_redland_sles_php56.Makefile" ersetzen (im Anhang)
     
    make
    make install

    Zum Schluss noch die redland.so installieren
    cp redland.so /usr/lib64/php56/extensions/
    cd /etc/php56/conf.d
    cp tokenizer.ini redland.ini
    vim redland.ini
    tokenizer.so durch redland.so ersetzen

    ### hier weiter

    # php5-intl mit Yast2 installieren. Wie auf hoerkaen/paideia von Hand. Ist evtl. im PHP schon mit drin.
sudo service php56-fpm restart

# Installiere to.science.drupal
Clone the repository and submodules to Drupal's module directory:
$ cd sites/all/modules
$ git clone https://github.com/hbz/to.science.drupal.git
$ cd to.science.drupal
$ git submodule update --init
Download non Drupal-core dependency modules:
$ cd sites/all/modules
$ curl https://ftp.drupal.org/files/projects/entity-7.x-1.1.tar.gz | tar xz
$ curl https://ftp.drupal.org/files/projects/entity_js-7.x-1.0-alpha3.tar.gz | tar xz
$ curl https://ftp.drupal.org/files/projects/ctools-7.x-1.3.tar.gz | tar xz

# Installiere ORCA drupal-theme
cd /opt/toscience/drupal/sites/all/themes
git clone https://github.com/hbz/edoweb-drupal-theme.git

  
 
Activate "Edoweb Entities" module (e.g. at <http://localhost/drupal/?q=admin/modules>) and confirm activation of dependency modules. Also activate the modules "chaos tools" and "entity tokens".


Make sure the "Local" module has been activated if you need to localize your installation. If you have not yet localized your installation, navigate to http://localhost/drupal?q=admin/config/regional/translate/import, choose your language file and the language to import it into.  Clear the cache to make sure all field instance labels are updated.

Finally, set the host, user and password for the API at <http://localhost/?q=edoweb/config/storage>  and <http://localhost/?q=edoweb/config/account>s.  Man gelangt auch über Startseite - Konfiguration - APIs bzw. Accounts zu diesen Seiten.

Activate "Edoweb" theme (e.g. at http://localhost/drupal/?q=admin/appearance).

Navigate to http://localhost/resource . This will show you the start page of edoweb.

# Connect to to.science.api
*****************************
Configuration against to.science.api takes place at

http://localhost/edoweb/config/storage  and  http://localhost/edoweb/config/accounts .

Please set the api host to the full url of your to.science.api installation, e.g. https://api.localhost.

If you have installed to.science.api as is, it will come with a faked user authentication that provides three users: edoweb-admin, edoweb-editor and edoweb-reader. With fake user authentication, the api will accept any password for the three users, so you can set all three passwords to an arbitrary string.

Drupal läuft über http://localhost/user

# Localization

To localize your Drupal installation, first activate the "Localize"
module. Then download your preffered language from
<https://localize.drupal.org/translate/languages/>. Navigate to
<http://localhost/drupal?q=admin/config/regional/translate/import>,
choose your language file and the language to import it into. To
localize regal-drupal (German file available [here](german.po), proceed
as describe above. Clear the cache to make sure all field instance
labels are updated.
