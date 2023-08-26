---
layout: default
title: Setup your Mac
permalink: /setup_mac
last_modified_date: '2023-08-25'
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


## Homebrew
Install the [Homebrew package manager](https://brew.sh/). This will allow you to install almost any app from the command line.  This provides the `brew` command used extensively in this guide.


```bash
/bin/bash -c "$(curl -fsSL https://raw.githubusercontent.com/Homebrew/install/HEAD/install.sh)"
```


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

# Install DDEV
brew install ddev/ddev/ddev

{: .note }
You might need to have your ssh certificate set up correctly before doing this step.

# Initialize mkcert
mkcert -install

This is the output which in this case is prompting to install nss if you have FireFox installed.  Don't forget that step.

```
Created a new local CA 💥

Sudo password:
The local CA is now installed in the system trust store! ⚡️

Warning: "certutil" is not available, so the CA can't be automatically installed in Firefox! 
Install "certutil" with "brew install nss" and re-run "mkcert -install"
```

### Stats
Show cpu/disk/network i/o stats in toolbar
```
brew install stats
```


## Terminal

### Iterm2 Terminal Replacement
[Download and install iTerm2](https://iterm2.com/)

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


## Resources
- [macOS Monterey: Setting up a Mac for Development by Tania Rascia Jan 2022](https://www.taniarascia.com/setting-up-a-brand-new-mac-for-development )
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
