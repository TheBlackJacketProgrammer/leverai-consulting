const gulp = require("gulp");
const concat = require("gulp-concat");
const uglify = require("gulp-uglify");

gulp.task("bundle-js", function () {
  return gulp
    .src([
      "app.js",
      "ng-variables.js",
      "ng-header.js",
      "ng-login.js",
      "ng-subscribe.js",
      "ng_dashboard-dev.js",
      "ng-dashboard-customer.js",
      "ng-dashboard-admin.js",
      "ng-header-admin.js",
      "ng-customer.js",
      "ng-billing.js",
      "ng-user-profile.js",
      "test-scripts/*.js"
    ])
    .pipe(concat("bundle.min.js"))
    .pipe(uglify())
    .pipe(gulp.dest("../dist/"));
});

gulp.task("bundle-js-dev", function () {
  return gulp
    .src([
      "app.js",
      "ng-variables.js",
      "ng-header.js",
      "ng-login.js",
      "ng-subscribe.js",
      "ng_dashboard-dev.js",
      "ng-dashboard-customer.js",
      "ng-dashboard-admin.js",
      "ng-header-admin.js",
      "ng-customer.js",
      "ng-billing.js",
      "ng-user-profile.js",
      "test-scripts/*.js"
    ])
    .pipe(concat("bundle.js"))
    .pipe(gulp.dest("../dist/"));
});

gulp.task("watch", function () {
  console.log("Starting watch task...");
  gulp.watch([
    "app.js",
    "ng-variables.js",
    "ng-header.js",
    "ng-login.js",
    "ng-subscribe.js",
    "ng_dashboard-dev.js",
    "ng-dashboard-customer.js",
    "ng-dashboard-admin.js",
    "ng-header-admin.js",
    "ng-customer.js",
    "ng-billing.js",
    "ng-user-profile.js",
    "test-scripts/*.js"
  ], function(cb) {
    console.log("Files changed, rebuilding...");
    gulp.series("bundle-js", "bundle-js-dev")(cb);
  });
  console.log("Watch task started successfully");
});
