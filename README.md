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

    $ sudo apt-get install php5-librdf
    $ sudo apt-get install php5-curl
    $ sudo apt-get install php5-intl

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

Activate "Edoweb Entities" module at (e.g. at
<http://localhost/drupal/?q=admin/modules>) and confirm activation of
dependency modules. Finally, set the host, user and password for the API
at <http://localhost/drupal/?q=admin/config/edoweb/storage>.

# Localization

To localize your Drupal installation, first activate the "Localize"
module. Then download your preffered language from
<https://localize.drupal.org/translate/languages/>. Navigate to
<http://localhost/drupal?q=admin/config/regional/translate/import>,
choose your language file and the language to import it into. To
localize regal-drupal (German file available [here](german.po), proceed
as describe above. Clear the cache to make sure all field instance
labels are updated.
