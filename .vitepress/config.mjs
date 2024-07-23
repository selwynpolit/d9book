import { defineConfig } from 'vitepress';
import { generateSidebar } from 'vitepress-sidebar';

// https://vitepress.dev/reference/site-config
export default defineConfig({
  title: 'Drupal at your Fingertips',
  description:
    'Drupal at your Fingertips: A developers quick reference for Drupal 9 and 10',
  base: '/',
  srcDir: './book',
  outDir: './dist',
  cleanUrls: true,
  lastUpdated: true,
  head: [
    ['link', { rel: 'icon', href: '/d9book/images/favicon.ico' }],
    [
      'script',
      {
        async: '',
        src: 'https://www.googletagmanager.com/gtag/js?id=G-8V22RQEJ71',
      },
    ],
    [
      'script',
      {},
      `window.dataLayer = window.dataLayer || [];
      function gtag(){dataLayer.push(arguments);}
      gtag('js', new Date());
      gtag('config', 'G-8V22RQEJ71');`,
    ],
  ],
  //rewrites: {
  //'nodes_n_fields.md': 'nodes-and-fields.md',
  //'off_the_island.md': 'off-island.md',
  //},

  vite: {
    ssr: {
      noExternal: ['@nolebase/vitepress-plugin-enhanced-readabilities'],
    },
  },
  themeConfig: {
    logo: '/images/d9book.svg',

    // https://vitepress.dev/reference/default-theme-config
    nav: [
      { text: 'Home', link: '/' },
      { text: 'About', link: '/about' },
      { text: 'Attribution', link: '/attribution' },
      { text: 'Contribute', link: '/contribute' },
      { text: 'Fork me', link: 'https://github.com/selwynpolit/d9book/fork' },
    ],

    outline: {
      level: [2, 3],
    },

    search: {
      provider: 'local',
    },

    editLink: {
      pattern: 'https://github.com/selwynpolit/d9book/edit/gh-pages/book/:path',
      text: 'Edit this page on GitHub',
    },

    sidebar: generateSidebar({
      documentRootPath: 'book',
      useTitleFromFrontmatter: true,
      sortMenusByName: true,
      hyphenToSpace: true,
      excludeFiles: [
        'about.md',
        'attribution.md',
        'mysteries.md',
        'contribute.md',
      ],
    }),

    socialLinks: [
      { icon: 'x', link: '//twitter.com/selwynpolit' },
      { icon: 'github', link: '//github.com/selwynpolit' },
    ],

    footer: {
      message:
        '<span>\n' +
        '  <a property="dct:title" rel="cc:attributionURL" href="//selwynpolit.github.io/d9book">Drupal at your fingertips</a>\n' +
        '  by <a rel="cc:attributionURL dct:creator" property="cc:attributionName" href="//drupal.org/u/selwynpolit">Selwyn Polit</a>\n' +
        '  is licensed under <a href="//creativecommons.org/licenses/by/4.0/" target="_blank" rel="license noopener noreferrer">CC BY 4.0\n' +
        '  </a><br>Drupal is a registered trademark of Dries Buytaert</span>',
    },
  },
});
