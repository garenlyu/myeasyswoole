<!DOCTYPE html>
<html>
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no, minimal-ui">
  <meta name="keywords" content="gay|lesbian|">
  <meta name="description" content="gay|lesbian">
  <link href="https://fonts.googleapis.com/css?family=Roboto:100,300,400,500,700,900" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/@mdi/font@4.x/css/materialdesignicons.min.css" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/vuetify@2.x/dist/vuetify.min.css" rel="stylesheet">
  <title>霓途</title>
  <style type="text/css">
    [v-cloak] {
      display: none;
    }
  </style>
</head>
<body>
  <div id="app" v-cloak>
    <v-app>
      <v-main class="blue lighten-4">
        <v-container>
          <v-app-bar app
          >
            <v-app-bar-nav-icon @click="publicData.drawer = true"></v-app-bar-nav-icon>
            <v-app-bar-title>霓途</v-app-bar-title>
          </v-app-bar>
          @yield('home')
        </v-container>

        <!-- 提示框 -->
        <v-snackbar
          v-model="publicData.snackbar"
          :color="publicData.snackbarColor"
          top
          timeout="5000">
          @{{ publicData.snackbarText }}
        </v-snackbar>

        <v-navigation-drawer
          v-model="publicData.drawer"
          absolute
          temporary
        >
          <v-list
            nav
          >
            <v-list-item-group
              v-model="publicData.activeMenu"
            >
              <v-list-item value="home" href="home">
                <v-list-item-icon>
                  <v-icon>mdi-home</v-icon>
                </v-list-item-icon>
                <v-list-item-title>Home</v-list-item-title>
              </v-list-item>

              <v-list-item value="friends" href="friends">
                <v-list-item-icon>
                  <v-icon>mdi-account-multiple</v-icon>
                </v-list-item-icon>
                <v-list-item-title>Friends</v-list-item-title>
              </v-list-item>

              <v-list-item value="moments" href="moments">
                <v-list-item-icon>
                  <v-icon>mdi-account-search</v-icon>
                </v-list-item-icon>
                <v-list-item-title>Discovery</v-list-item-title>
              </v-list-item>
            </v-list-item-group>
          </v-list>
        </v-navigation-drawer>
      </v-main>
    </v-app>
  </div>

  <script src="https://cdn.jsdelivr.net/npm/vue@2.x/dist/vue.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/vuetify@2.x/dist/vuetify.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/axios@0.20.0/dist/axios.min.js"></script>
  <script src="/static/js/app.js?202108310949"></script>
  @yield('script')
</body>
</html>