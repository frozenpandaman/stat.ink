{{strip}}
{{\app\assets\AppAsset::register($this)|@void}}
{{\app\assets\GithubForkRibbonJsAsset::register($this)|@void}}
{{$this->beginPage()|@void}}
  <!DOCTYPE html>
  <html lang="{{$app->language|escape}}">
    <head>
      <meta charset="UTF-8">
      <meta http-equiv="X-UA-Compatible" content="IE=edge">
      <meta name="viewport" content="width=device-width, initial-scale=1">
      <meta name="apple-mobile-web-app-capable" content="yes">
      <meta name="format-detection" content="telephone=no,email=no,address=no">
      {{\yii\helpers\Html::csrfMetaTags()}}
      <title>{{$this->title|default:$app->name|default:'stat.ink'|escape}}</title>
      {{\app\components\helpers\I18n::languageLinkTags()}}
      {{$this->head()}}
    </head>
    <body>
      {{$this->beginBody()|@void}}
        {{include '@app/views/layouts/navbar.tpl'}}
        {{$content}}
        {{include '@app/views/layouts/footer.tpl'}}
        <span id="event"></span>
        {{if $app->params.googleAnalytics != ''}}
          {{use class="\cybercog\yii\googleanalytics\widgets\GATracking" type="function"}}
          {{GATracking trackingId=$app->params.googleAnalytics}}
        {{/if}}
      {{$this->endBody()|@void}}
    </body>
  </html>
{{$this->endPage()|@void}}
{{/strip}}
