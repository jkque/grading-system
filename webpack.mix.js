const path = require('path')
const mix = require('laravel-mix')
// const { BundleAnalyzerPlugin } = require('webpack-bundle-analyzer')

mix.config.vue.esModule = true

mix
  .js('resources/js/app.js', 'public/js')
  .sass('resources/sass/app.scss', 'public/css')

  .sourceMaps()
  .disableNotifications()

if (mix.inProduction()) {
  mix.version()

  mix.extract([
    'vue',
    'vform',
    'axios',
    'vuex',
    'jquery',
    'popper.js',
    'vue-i18n',
    'vue-meta',
    'js-cookie',
    'bootstrap',
    'vue-router',
    'sweetalert2',
    'vuex-router-sync',
    '@fortawesome/vue-fontawesome',
    '@fortawesome/fontawesome-svg-core',
    '@coreui/coreui',
    '@coreui/coreui-plugin-chartjs-custom-tooltips',
    '@coreui/icons',
    '@coreui/vue',
    'bootstrap-vue',
    'chart.js',
    'core-js',
    'css-vars-ponyfill',
    'flag-icon-css',
    'font-awesome',
    'perfect-scrollbar',
    'simple-line-icons',
    'vue-chartjs',
    'vue-perfect-scrollbar',
    'vue-bootstrap-datetimepicker',
    'vue-js-toggle-button'
  ])
}

mix.webpackConfig({
  plugins: [
    // new BundleAnalyzerPlugin()
  ],
  resolve: {
    extensions: ['.js', '.json', '.vue'],
    alias: {
      '~': path.join(__dirname, './resources/js')
    }
  },
  output: {
    chunkFilename: 'js/[name].[chunkhash].js',
    publicPath: mix.config.hmr ? '//localhost:8080' : '/'
  }
})
