---
title: Setup your Mac
---

# Setting up your Mac for Drupal development
![views](https://api.visitor.plantree.me/visitor-badge/pv?label=views&color=informational&namespace=d9book&key=setup_mac.md)

## Overview
Setting up your Mac for development is a highly personal process.  Everyone has their own preferences.  I've collected some practices that work well for me here.

## Better start with these

### Display files that start with .

Open finder and press Cmd-Shift-.

::: tip Note
This is a toggle, so if you press it twice, it will turn the `.` files off again.
:::

### Set fast keyboard repeat and short delay

In keyboard settings, set key repeat rate to the maximum and delay until repeat to the minimum.


### Display path bar at bottom of finder window

Open finder, in the top menu, select view and `Show Path Bar`. This will add a path bar at the bottom of the finder window.  This is useful for copying the path to a file or folder.  You can also navigate to a folder by clicking on the folder in the path bar.



### Set main display monitor

In the System settings, displays, set your main display monitor to the monitor that you want.  Otherwise things pop up on the other monitors.  

## Show speaker icon in menu bar

If the Sound control isn't in the menu bar, choose Apple menu > System Settings, then click Control Center in the sidebar. (You may need to scroll down.) Click the pop-up menu next to Sound on the right, then choose whether to show Sound in the menu bar all the time or only when it's active.

## SSH Keys

To generate a 4096 byte (4K) key use these commands and just hit return when prompted.  Don't enter a passphrase.:

```bash
cd ~/.ssh
ssh-keygen -t rsa -b 4096 -C "johnsmith@gmail.com" 
```
::: tip Note
Replace johnsmith@gmail.com with your email.
:::

You will need to add the ssh key to the agent permanently.  
for older versions of MacOS:
```
ssh-add -K ~/.ssh/id_rsa
```
for newer:
```
ssh-add --apple-use-keychain ~/.ssh/id_rsa
```


To list all the keys (or confirm that you successfully added the key to the agent.)
```
ssh-add -l
```


To remove an entry from ~/.ssh/known_hosts
```
ssh-keygen -R pogoacademystg.ssh.prod.acquia-sites.com
```


To copy the public key to the clipbpoard for pasting into Acquia/Github/Gitlab etc.
```
$ pbcopy < ~/.ssh/id_rsa.pub
```
[More at https://apple.stackexchange.com/questions/48502/how-can-i-permanently-add-my-ssh-private-key-to-keychain-so-it-is-automatically](https://apple.stackexchange.com/questions/48502/how-can-i-permanently-add-my-ssh-private-key-to-keychain-so-it-is-automatically)


And [https://help.github.com/articles/generating-a-new-ssh-key-and-adding-it-to-the-ssh-agent/](https://help.github.com/articles/generating-a-new-ssh-key-and-adding-it-to-the-ssh-agent/)
and for multiple keys: [https://gist.github.com/jexchan/2351996](https://gist.github.com/jexchan/2351996)




## Homebrew
Install the [Homebrew package manager](https://brew.sh/). This will allow you to install almost any app from the command line.  This provides the `brew` command used extensively in this guide.


```bash
/bin/bash -c "$(curl -fsSL https://raw.githubusercontent.com/Homebrew/install/HEAD/install.sh)"
```

::: tip Note
once you install a formula with Hombrew, you might want to see the `info` that was displayed after you ran the `brew install` command.  This is that crucial info that you need to complete the installation.  Do that with `brew info formula` e.g.:
:::

`brew info php@8.1` or `brew info jq`


## PHP
I like to install php 8.1 so I can run composer and drush commands in the terminal without having to first ssh into the DDEV docker containers.

```
brew install php@8.1
```
Be sure to run these scripts to put php 8.1 first in your PATH:

```
echo 'export PATH="/opt/homebrew/opt/php@8.1/bin:$PATH"' >> ~/.zshrc
echo 'export PATH="/opt/homebrew/opt/php@8.1/sbin:$PATH"' >> ~/.zshrc
```

Add some settings to let you run drush:

check to make sure this is the place to add your custom settings.ini
```
php --ini
```
You should see:
```
Configuration File (php.ini) Path: /opt/homebrew/etc/php/8.1
Loaded Configuration File:         /opt/homebrew/etc/php/8.1/php.ini
Scan for additional .ini files in: /opt/homebrew/etc/php/8.1/conf.d
Additional .ini files parsed:      /opt/homebrew/etc/php/8.1/conf.d/ext-opcache.ini,
```

Add a custom file e.g. `/opt/homebrew/etc/php/8.1/conf.d/selwyn.ini` with the following contents

```php
memory_limit = 1024M
max_execution_time = 30
upload_max_filesize = 200M
post_max_size = 256M
; How many GET/POST/COOKIE input variables may be accepted
max_input_vars = 5000
date.timezone = America/Chicago
error_reporting = E_ALL & ~E_DEPRECATED
```

To test that your settings are in place, run`php --ini`.  Notice the last line was added indicating that your custom php settings file was loaded.

```
Configuration File (php.ini) Path: /opt/homebrew/etc/php/8.1
Loaded Configuration File:         /opt/homebrew/etc/php/8.1/php.ini
Scan for additional .ini files in: /opt/homebrew/etc/php/8.1/conf.d
Additional .ini files parsed:      /opt/homebrew/etc/php/8.1/conf.d/ext-opcache.ini,
/opt/homebrew/etc/php/8.1/conf.d/selwyn.ini
```

::: tip Note
If you install composer first, you might end up with php 8.2 installed which has some challenges running the Drupal Test Traits and PHPUnit.
:::

## Composer

```
brew install composer
```

::: tip Note
Ideally install this after installing PHP@8.1 to avoid this putting PHP 8.2 (or later) first in the path.  This could cause some challenges running the Drupal Test Traits and PHPUnit.
:::


## Browsers
- [Firefox](https://www.mozilla.org/en-US/firefox/products/)
- [Chrome](https://www.google.com/chrome/)
- [Brave](https://brave.com/)
- [Opera](https://www.opera.com/)

## Dev tools

- [Phpstorm](https://www.jetbrains.com/phpstorm/)
- [VScode](https://code.visualstudio.com/)
- [Docker](https://docs.docker.com/desktop/install/mac-install/)


## DDEV

Install ddev

[From the DDEV docs website](https://ddev.readthedocs.io/en/latest/users/install/ddev-installation/#macos)

brew install ddev/ddev/ddev

::: tip Note
You might need to have your ssh certificate set up correctly before doing this step.
:::

Initialize mkcert
mkcert -install

This is the output which in this case is prompting to install nss if you have FireFox installed.  Don't forget that step.

```
Created a new local CA 💥

Sudo password:
The local CA is now installed in the system trust store! ⚡️

Warning: "certutil" is not available, so the CA can't be automatically installed in Firefox! 
Install "certutil" with "brew install nss" and re-run "mkcert -install"
```

## Sequel Ace
This is a great GUI SQL tool.

`brew install --cask sequel-ace`




## Terminal

### Iterm2 Terminal Replacement
[Download and install iTerm2](https://iterm2.com/)

Follow [instructions here](https://iterm2.com/documentation-shell-integration.html) to install Shell integration. The easiest way to install shell integration is to select the iTerm2>Install Shell Integration menu item. It will download and run a shell script.  This enables command history in the toolbelt.  Try it.  You'll love it!


### Oh My ZSH 
This is a \"helper\" program and bunch of useful plugins etc to enhance the ZSH shell that comes with the current MacOS.
[See](https://ohmyz.sh/)

```bash
sh -c "$(curl -fsSL https://raw.githubusercontent.com/ohmyzsh/ohmyzsh/master/tools/install.sh)"
```

#### ZSH auto suggestion plugin

[From the zsh-autosuggestions repo:](https://github.com/zsh-users/zsh-autosuggestions/blob/master/INSTALL.md)

```bash
git clone https://github.com/zsh-users/zsh-autosuggestions ${ZSH_CUSTOM:-~/.oh-my-zsh/custom}/plugins/zsh-autosuggestions
```
This will load the folder zsh-autosuggestions into the `~/oh-my-zsh/custom/plugins` folder

Edit your `.zshrc` to make sure in your `~/.zshrc` you have the zsh-autosuggestions in your plugins as below:

```
plugins=(git z macos zsh-autosuggestions zsh-syntax-highlighting sudo)
```



#### ZSH syntax highlighting

[From the repo for zsh-syntax-highlighting:](https://github.com/zsh-users/zsh-syntax-highlighting/blob/master/INSTALL.md)

```bash
git clone https://github.com/zsh-users/zsh-syntax-highlighting.git
echo "source ${(q-)PWD}/zsh-syntax-highlighting/zsh-syntax-highlighting.zsh" >> ${ZDOTDIR:-$HOME}/.zshrc
```
Move the `~/zsh-syntax-highlighting` into the `~/.oh-my-zsh/custom/plugins` folder.

In `~/.zshrc file` make sure in your `~/.zshrc` you have the `zsh-autosuggestions` in your plugins as below:

```
plugins=(git z macos zsh-autosuggestions zsh-syntax-highlighting sudo)
```



## Command line tools

### git

Although the macOS comes with git, it is probably wise to install the latest with homebrew using the following command:

```
brew install git
```

#### .gitconfig 
In your $HOME directory, create the .gitconfig file.  Replace my name and email address with yours.  I've added some useful aliases which allow nice shorthand commands like `git co branch_abc` `git hist` etc.

```
# This is Git's per-user configuration file.
[user]
  name = Selwyn Polit
  email = selwynpolit@example.com
[core]
  excludesfile = /Users/spolit/.gitignore_global

[alias]
  co = checkout
  ci = commit
  st = status
  br = branch
  hist = log --pretty=format:\"%h %ad | %s%d [%an]\" --graph --date=short
  plog = log --graph --pretty=format:'%Cred%h%Creset -%C(yellow)%d%Creset %s %Cgreen(%cr) %C(bold blue)<%an>%Creset' --abbrev-commit

[pull]
        rebase = false
[filter "lfs"]
        clean = git-lfs clean -- %f
        smudge = git-lfs smudge -- %f
        process = git-lfs filter-process
        required = true

```

Also to set the default main branch as `main` rather than the old and somewhat oppressive word `master` use: 

```sh
git config --global init.defaultBranch main
```


#### .gitignore_global

In your $HOME directory, create the .gitignore_global file.  


```
# Borrowed from https://gist.github.com/octocat/9257657
# Vim patterns from https://github.com/github/gitignore

# Ignore Emacs and Vim auto backup files
*~
[#]*[#]
*.swp
*.swo

# Ignore PHP Storm project files
.idea/

# Ignore Sublime project files
*.sublime-project
*.sublime-workspace

# Ignore Codekit files
*.codekit

# Ignore logs and databases
*.log
*.sql
*.sql.gz
*.sqlite

# Ignore SASS cache files
*.scssc

# OS generated files #
######################
.DS_Store
.DS_Store?
._*
.Spotlight-V100
.Trashes
ehthumbs.db
Thumbs.db

# Vim temp files #
##################
[._]*.s[a-w][a-z]
[._]s[a-w][a-z]
*.un~
Session.vim
.netrwhist
*~

# Acquia CLI
.acquia-cli.yml


```


### NVM (Node Version Manager)

`brew install nvm`

Add the following to the end of your `~/.zshrc` file:

```
  export NVM_DIR="$HOME/.nvm"
  [ -s "/opt/homebrew/opt/nvm/nvm.sh" ] && \. "/opt/homebrew/opt/nvm/nvm.sh"  # This loads nvm
  [ -s "/opt/homebrew/opt/nvm/etc/bash_completion.d/nvm" ] && \. "/opt/homebrew/opt/nvm/etc/bash_completion.d/nvm"  # This loads nvm bash_completion
```

To test if it installed correctly, use: 

```sh
nvm --version
0.39.7
```

### bat

[bat(https://github.com/sharkdp/bat)] is a replacement for cat, does beautiful syntax highlighting

```
brew install bat
```


### cloc
Count lines of code.

```
brew install cloc
```


### acli
[Acquia CLI tool](https://docs.acquia.com/acquia-cli/install/)

```
curl -OL https://github.com/acquia/cli/releases/latest/download/acli.phar
chmod +x acli.phar
```
For some reason, I got permission denied for the suggested command: 
```
mv acli.phar /usr/local/bin/acli
```
so I rather moved it to my ~/bin with

```
mv acli.phar ~/bin 
```
which is in my path.  Then authorize acli's login with:

```
acli auth:login
```

Follow prompts, setup API token etc

::: tip Note
You can make the bin directory if it doesn't exist with:

```sh
mkdir ~/bin
```

Then add it to your path with by adding the following to the end of your `~/.zshrc` file:

```
export PATH="$HOME/bin:$PATH"
```
:::


### jq

jq is a tool for processing JSON inputs, applying the given filter to
its JSON text inputs and producing the filter's results as JSON on
standard output.

```
brew install jq
```

### wget

GNU Wget is a free software package for retrieving files using HTTP, HTTPS, FTP and FTPS, the most widely used Internet protocols. You can use wget for running Drupal cron.

```
brew install wget
```


## Drush

### Global Drush

I find that installing drush version 8 globally is most convenient for my Drupal development as I frequently run drush commands in the terminal and really like the command completion afforded my Oh-my-Zsh.  Drush runs slower than the equivalent `ddev drush` commands when installed this way. The host drush version doesn't matter very much since it is only used to find the proper drush version (most likely within /vendor/bin) and call it. Always install drush in each project using composer.

::: warning
You should be aware that you might get unpredictable results if you use differing versions of PHP on your local vs in the DDEV containers.  E.g. if your local mac has PHP 7 and your DDEV is using PHP 8.1, you are likely to have unpredictable results when you issue some drush commands.  Generally speaking I haven't seen things be too wacky, but you should be aware of this.
:::

Don't use homebrew to install drush. Rather use the composer version:

```
composer global require drush/drush ^8
```
Then add Drush to your system path by placing the following in your ~/.zshrc ( or if using bash: `~/.bash_profile`:

```
 export PATH="$HOME/.composer/vendor/bin:$PATH" 
``` 

::: tip Note
Test any of these path changes by running `source ~/.zshrc` to reload the environment variables.  You can also open a new iterm window if you prefer.
:::

By setting up drush globally, you can navigate into a Drupal directory e.g. (`~/Sites/apc`) and issue drush commands e.g. 

`drush cr` or `drush cst` etc.

As of November 2023 and v1.22.4+ to allow local drush on host you will need to install the following Ddev addon:

`ddev get rfay/ddev-drushonhost`

See <https://github.com/rfay/ddev-drushonhost> for documentation

You will need: 
`export IS_DDEV_PROJECT=true`

OR

In your project `settings.php` make sure the last part of the file looks like this (the order is critical):

```php
if (file_exists($app_root . '/' . $site_path . '/settings.local.php')) {
  include $app_root . '/' . $site_path . '/settings.local.php';
}

// Automatically generated include for settings managed by ddev.
$ddev_settings = dirname(__FILE__) . '/settings.ddev.php';
if (getenv('IS_DDEV_PROJECT') == 'true' && is_readable($ddev_settings)) {
  require $ddev_settings;
}

```

 Then add this to your `settings.local.php`:
`putenv("IS_DDEV_PROJECT=true");`

Discussion: <https://github.com/ddev/ddev/pull/5328>

Restart the project with `ddev restart`.

Et voila!  You can now issue command such as `drush cr` as if you had first `ssh'ed` into the container.  


**Troubleshooting**
Failing looks like this:

```sh
$ drush cr

In Database.php line 378:

The specified database connection is not defined: default
```

Try a `ddev restart`

## Drupal Check

Built on PHPStan, this static analysis tool will check for correctness (e.g. using a class that doesn't exist), deprecation errors, and more.

Why? While there are many static analysis tools out there, none of them run with the Drupal context in mind. This allows checking contrib modules for deprecation errors thrown by core.

To install [drupal-check](https://github.com/mglaman/drupal-check) use:

```
composer global require mglaman/drupal-check
```

## PhpStorm
I find this to be a powerful tool in my Drupal development.  

### Plugins

I like the following plugins:
- GitHub Copilot
- Rainbow CSV
- PHP Annotations


### Code Sniffing

You can set up PhpStorm to automatically look at your code and warn you of lines that do not meet [Drupal Coding Standards](https://www.drupal.org/docs/develop/standards).  

Go to: Settings, Php, Quality Tools, PHP_CodeSniffer

![Image of PHP Codesniffer settings](/images/phpstorm_codesniffer.png)

Use the following settings:
- Configuration: `System PHP`
- Check files with extensions: `php,js,css,inc, module`
- Check Show warning as: `Warning`
- Check Show sniff name

- **If you installed the coder module in your project** (with `composer require drupal/coder`):
  - Check `Installed standards path` and set the path to: `/Users/spolit/Sites/tea/vendor/drupal/coder/coder_sniffer` Replace this with the path to your project. Later you will need to  unchcheck the checkbox.. Really!
  - Be sure to set Coding standard to: `Drupal`.  If this option isn't shown, follow the steps below, click ok and then open the settings dialog again.  Hopefully it will show up then.
  - After checking `installed standards path` and providing the path above, it seems you must uncheck `installed standards path` for this to keep working. I know, weird, right?
  If you installed the coder module in your project: Under the ... button (on the right side of the screen next to `Show ignored files`), set the PHP_CodeSniffer path to: `/Users/spolit/Sites/tea/vendor/bin/phpcs` and the Path to phpcbf to `/Users/spolit/Sites/tea/vendor/bin/phpcbf`. 
![](/images/PHPStorm_PHP_Codesniffer_settings.png)


- **If you have phpcs installed globally** (with `composer global require drupal/coder`):
  - Check `Installed standards path` and set it to: `/Users/spolit/.composer/vendor/drupal/coder/coder_sniffer` (Replace this with the path to your global composer directory.)
  - Be sure to set Coding standard to: `Drupal`.  If this option isn't shown, follow the steps below, click ok and then open the settings dialog again.  Hopefully it will show up then.
  - After checking `installed standards path` and providing the path above, it seems you must uncheck `installed standards path` for this to keep working. I know, weird, right?
  - If you have installed phpcs and coder globally, Under the ... button (on the right side of the screen next to `Show ignored files`), set the PHP_CodeSniffer path to: `/Users/spolit/.composer/vendor/bin/phpcs` and the Path to phpcbf to `/Users/spolit/.composer/vendor/bin/phpcbf`. 

![](/images/PHPStorm_PHP_Codesniffer_settings.png)


::: tip Note
(replace `/Users/spolit` with your own path to your username and `tea` with the name of the directory for your site.) 
:::

More at [PhpStorm PHP_Codesniffer docs](https://www.jetbrains.com/help/phpstorm/using-php-code-sniffer.html).

For troubleshooting, see [this issue on Drupal.org](https://www.drupal.org/project/coder/issues/3262291#comment-15212485) especially if you see this annoying issue:

```
phpcs: ERROR: Referenced sniff "SlevomatCodingStandard.ControlStructures.RequireNullCoalesceOperator" does not exist  
Run "phpcs --help" for usage information 
```

![Error messages in PHPStorm](/images/PHPStorm_codesniffer_errors.png)


To install phpcs & phpcf *globally* use: 
`composer global require squizlabs/php_codesniffer`
To install coder module *globally* so you can codesniff using Drupal standards, use `composer global require drupal/coder`

To use phpcs on the command line with a global installation of codesniffer, use:

`phpcs web/sites/default/settings.php`



## Super useful utilities

These are some useful mac utilities that make my life a little better.

### Stats

Show cpu/disk/network i/o stats in toolbar

```
brew install stats
```
Run stats from applications folder, in settings, select start at login.


### ngrok

ngrok lets you quickly share a site you are developing on with others. From the ddev docs: `ddev share` proxies the project via ngrok for sharing your project with others on your team or around the world. It’s built into DDEV and requires an [ngrok.com](https://ngrok.com) account. Run `ddev share` and then give the resultant URL to your collaborator or use it on your mobile device. More at [https://ddev.readthedocs.io/en/latest/users/topics/sharing/](https://ddev.readthedocs.io/en/latest/users/topics/sharing/)


```
brew install ngrok
```

### rectangle

Rectangle is a window management app based on Spectacle, written in Swift. [More at https://github.com/rxhanson/Rectangle](https://github.com/rxhanson/Rectangle)

```
brew install --cask rectangle
```

## Resources
- [macOS Monterey: Setting up a Mac for Development by Tania Rascia Jan 2022](https://www.taniarascia.com/setting-up-a-brand-new-mac-for-development)
- [How I upgrade my Mac for development in Catalina macOS by SaKKo May 2021](https://dev.to/sakko/how-i-upgrade-my-mac-for-development-in-catalina-macos-33g1) - This covers xcode, git, iterm2, oh my zsh, NVM, Ruby, Homebrew, Postgresql, MySql, ElasticSearch, Redis and other apps. 
