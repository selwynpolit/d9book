---
layout: default
title: Setup your Mac
permalink: /setup_mac
last_modified_date: '2023-09-27'
---

# Setting up your Mac for Drupal development
{: .no_toc .fw-500 }

## Table of contents
{: .no_toc .text-delta }

- TOC
{:toc}

![views](https://api.visitor.plantree.me/visitor-badge/pv?label=views&color=informational&namespace=d9book&key=setup_mac.md)

---

## Overview
Setting up your Mac for development is a highly personal process.  Everyone has their own preferences.  I've collected some practices that work well for me here.

## Better start with these

### Display files that start with .

Open finder and press Cmd-Shift-.

{: .note }
This is a toggle, so if you press it twice, it will turn the `.` files off again.

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
{: .note }
Replace johnsmith@gmail.com with your email.

You will need to add the ssh key to the agent permanently.  
for older versions of MacOS:
`ssh-add -K ~/.ssh/id_rsa`
for newer:
`ssh-add --apple-use-keychain ~/.ssh/id_rsa`


To list all the keys (or confirm that you successfully added the key to the agent.)
`ssh-add -l`


To remove an entry from ~/.ssh/known_hosts
`ssh-keygen -R pogoacademystg.ssh.prod.acquia-sites.com`


To copy the public key to the clipbpoard for pasting into Acquia/Github/Gitlab etc.
```
$ pbcopy < ~/.ssh/id_rsa.pub
```
From: https://apple.stackexchange.com/questions/48502/how-can-i-permanently-add-my-ssh-private-key-to-keychain-so-it-is-automatically



More info at: [https://help.github.com/articles/generating-a-new-ssh-key-and-adding-it-to-the-ssh-agent/](https://help.github.com/articles/generating-a-new-ssh-key-and-adding-it-to-the-ssh-agent/)
and for multiple keys: [https://gist.github.com/jexchan/2351996](https://gist.github.com/jexchan/2351996)




## Homebrew
Install the [Homebrew package manager](https://brew.sh/). This will allow you to install almost any app from the command line.  This provides the `brew` command used extensively in this guide.


```bash
/bin/bash -c "$(curl -fsSL https://raw.githubusercontent.com/Homebrew/install/HEAD/install.sh)"
```

{: .note }
once you install a formula with Hombrew, you might want to see the `info` that was displayed after you ran the `brew install` command.  This is that crucial info that you need to complete the installation.  Do that with `brew info formula` e.g.:

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

```
memory_limit = 1024M
max_execution_time = 30
upload_max_filesize = 200M
post_max_size = 256M
; How many GET/POST/COOKIE input variables may be accepted
max_input_vars = 5000
date.timezone = America/Chicago
error_reporting = E_ALL & ~E_DEPRECATED
```

now `php --ini` should report

```
Configuration File (php.ini) Path: /opt/homebrew/etc/php/8.1
Loaded Configuration File:         /opt/homebrew/etc/php/8.1/php.ini
Scan for additional .ini files in: /opt/homebrew/etc/php/8.1/conf.d
Additional .ini files parsed:      /opt/homebrew/etc/php/8.1/conf.d/ext-opcache.ini,
/opt/homebrew/etc/php/8.1/conf.d/selwyn.ini
```


```
vim /opt/homebrew/etc/php/8.1/conf.d/myphp.ini
```

Add the following:

```php
memory_limit = 1024M
max_execution_time = 30
upload_max_filesize = 200M
post_max_size = 256M
; How many GET/POST/COOKIE input variables may be accepted
max_input_vars = 5000
date.timezone = America/Chicago
```

run this to confirm your changes are in place:

```
php --ini
```
And you should see this.  Notice the last line was added:

```
Configuration File (php.ini) Path: /opt/homebrew/etc/php/8.1
Loaded Configuration File:         /opt/homebrew/etc/php/8.1/php.ini
Scan for additional .ini files in: /opt/homebrew/etc/php/8.1/conf.d
Additional .ini files parsed:      /opt/homebrew/etc/php/8.1/conf.d/ext-opcache.ini,
/opt/homebrew/etc/php/8.1/conf.d/myphp.ini
```


{: .note }
If you install composer first, you might end up with php 8.2 installed which has some challenges running the Drupal Test Traits and PHPUnit.




## Composer

```
brew install composer
```

{: .note }
Ideally install this after installing PHP@8.1 to avoid this putting PHP 8.2 (or later) first in the path.  This could cause some challenges running the Drupal Test Traits and PHPUnit.



## Browsers
- [Firefox](https://www.mozilla.org/en-US/firefox/products/)
- [Chrome](https://www.google.com/chrome/)
- [Brave](https://brave.com/)
- [Opera](https://www.opera.com/)

## Dev tools

- [Phpstorm](https://www.jetbrains.com/phpstorm/)
- [VScode](https://code.visualstudio.com/)
- [Docker](https://docs.docker.com/desktop/install/mac-install/)


### DDEV

Install ddev

[From the DDEV docs website](https://ddev.readthedocs.io/en/latest/users/install/ddev-installation/#macos)

brew install ddev/ddev/ddev

{: .note }
You might need to have your ssh certificate set up correctly before doing this step.

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
git clone https://github.com/zsh-users/zsh-autosuggestions \${ZSH_CUSTOM:-~/.oh-my-zsh/custom}/plugins/zsh-autosuggestions
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



### jq

jq is a tool for processing JSON inputs, applying the given filter to
its JSON text inputs and producing the filter's results as JSON on
standard output.

```
brew install jq
```

### wget

GNU Wget is a free software package for retrieving files using HTTP, HTTPS, FTP and FTPS, the most widely used Internet protocols.

```
brew install wget
```


## Drush

### Global Drush
I find that installing drush version 8 globally is best for my setup. Don't use homebrew to install drush. Rather use the composer version.

```
composer global require drush/drush ^8
```
Then add Drush to your system path by placing:

```
 export PATH="$HOME/.composer/vendor/bin:$PATH" 
``` 
into your ~/.zshrc ( or if using bash: `~/.bash_profile`

{: .note }
Test any of these path changes by running source ~/.zshrc to reload the environment variables.  You can also open a new iterm window if you prefer.


## Drupal Check

Built on PHPStan, this static analysis tool will check for correctness (e.g. using a class that doesn't exist), deprecation errors, and more.

Why? While there are many static analysis tools out there, none of them run with the Drupal context in mind. This allows checking contrib modules for deprecation errors thrown by core.

To install [drupal-check](https://github.com/mglaman/drupal-check) use:

```
composer global require mglaman/drupal-check
```

## PhpStorm
I find this to be a powerful tool in my Drupal development.  

### Code Sniffing

You can set up PhpStorm to automatically look at your code and warn you of lines that do not meet [Drupal Coding Standards](https://www.drupal.org/docs/develop/standards).  

Go to: Settings, Php, Debug, Quality Tools, PHP_CodeSniffer

Use the following settings:
- Configuration: System PHP
- Coding standard: Drupal

Under the ... button set the PHP_CodeSniffer path to : /Users/spolit/.composer/vendor/bin/phpcs
If you have installed phpcs globally, this is the correct path to use. If you have installed PHP_CodeSniffer in your project locally, you could use a path like: `/Users/spolit/Sites/tea/vendor/bin/phpcs` and it will work fine.

{: .note }
(replace `/Users/spolit` with your own path to your username) 

More at [PhpStorm PHP_Codesniffer docs](https://www.jetbrains.com/help/phpstorm/using-php-code-sniffer.html).






## Super useful utilities

These are some useful mac utilities that make my life a little better.

### Stats

Show cpu/disk/network i/o stats in toolbar

```
brew install stats
```
Run stats from applications folder, in settings, select start at login.


### ngrok

ngrok is a secure unified ingress platform that combines your reverse proxy, firewall, API gateway and global load balancing into a production service.

```
brew install ngrok
```


## Resources
- [macOS Monterey: Setting up a Mac for Development by Tania Rascia Jan 2022](https://www.taniarascia.com/setting-up-a-brand-new-mac-for-development)
- [How I upgrade my Mac for development in Catalina macOS by SaKKo May 2021](https://dev.to/sakko/how-i-upgrade-my-mac-for-development-in-catalina-macos-33g1) - This covers xcode, git, iterm2, oh my zsh, NVM, Ruby, Homebrew, Postgresql, MySql, ElasticSearch, Redis and other apps. 


---

<script src="https://giscus.app/client.js"
        data-repo="selwynpolit/d9book"
        data-repo-id="MDEwOlJlcG9zaXRvcnkzMjUxNTQ1Nzg="
        data-category="Q&A"
        data-category-id="MDE4OkRpc2N1c3Npb25DYXRlZ29yeTMyMjY2NDE4"
        data-mapping="title"
        data-strict="0"
        data-reactions-enabled="1"
        data-emit-metadata="0"
        data-input-position="bottom"
        data-theme="preferred_color_scheme"
        data-lang="en"
        crossorigin="anonymous"
        async>
</script>
