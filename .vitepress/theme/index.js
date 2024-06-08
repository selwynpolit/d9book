// https://vitepress.dev/guide/custom-theme
import { useData, useRoute } from 'vitepress';
import vitepressBackToTop from 'vitepress-plugin-back-to-top';
import giscusTalk from 'vitepress-plugin-comment-with-giscus';
import googleAnalytics from 'vitepress-plugin-google-analytics';
import imageViewer from 'vitepress-plugin-image-viewer';
import DefaultTheme from 'vitepress/theme';
import { h } from 'vue';
import { NolebaseEnhancedReadabilitiesMenu } from '@nolebase/vitepress-plugin-enhanced-readabilities/client';

import 'viewerjs/dist/viewer.min.css';
import '@nolebase/vitepress-plugin-enhanced-readabilities/client/style.css';
import './style.css';
import './styles/components/vp-doc.css';

/** @type {import('vitepress').Theme} */
export default {
  extends: DefaultTheme,
  Layout: () => {
    return h(DefaultTheme.Layout, null, {
      // https://vitepress.dev/guide/extending-default-theme#layout-slots
      // A enhanced readabilities menu for wider screens
      'nav-bar-content-after': () => h(NolebaseEnhancedReadabilitiesMenu),
    });
  },
  enhanceApp({ app, router, siteData }) {
    vitepressBackToTop({ threshold: 300 });
    googleAnalytics({ id: '8V22RQEJ71' });
  },
  setup: () => {
    // Get frontmatter and route
    const { frontmatter } = useData();
    const route = useRoute();

    imageViewer(route);

    // Obtain configuration from: https://giscus.app/
    giscusTalk(
      {
        repo: 'selwynpolit/d9book',
        repoId: 'MDEwOlJlcG9zaXRvcnkzMjUxNTQ1Nzg=',
        category: 'Q&A', // default: `General`
        categoryId: 'MDE4OkRpc2N1c3Npb25DYXRlZ29yeTMyMjY2NDE4s',
        mapping: 'title', // default: `pathname`
        inputPosition: 'bottom', // default: `top`
        lang: 'en', // default: `zh-CN`
        lightTheme: 'light', // default: `light`
        darkTheme: 'transparent_dark', // default: `transparent_dark`
        // ...
      },
      {
        frontmatter,
        route,
      },
      // Whether to activate the comment area on all pages.
      // The default is true, which means enabled, this parameter can be ignored;
      // If it is false, it means it is not enabled.
      // You can use `comment: true` preface to enable it separately on the page.
      true,
    );
  },
};
