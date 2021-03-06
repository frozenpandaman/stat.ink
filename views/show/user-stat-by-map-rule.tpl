{{strip}}
  {{set layout="main.tpl"}}

  {{$name = '{0}-san'|translate:'app':$user->name}}
  {{$title = "{0}'s Battle Stats (by Mode and Map)"|translate:'app':$name}}
  {{set title="{{$app->name}} | {{$title}}"}}

  {{$this->registerMetaTag(['name' => 'twitter:card', 'content' => 'summary'])|@void}}
  {{$this->registerMetaTag(['name' => 'twitter:title', 'content' => $title])|@void}}
  {{$this->registerMetaTag(['name' => 'twitter:description', 'content' => $title])|@void}}
  {{$this->registerMetaTag(['name' => 'twitter:site', 'content' => '@stat_ink'])|@void}}
  {{if $user->twitter != ''}}
    {{$this->registerMetaTag(['name' => 'twitter:creator', 'content' => '@'|cat:$user->twitter])|@void}}
  {{/if}}

  {{\jp3cki\yii2\flot\FlotPieAsset::register($this)|@void}}
  <div class="container">
    <h1>
      {{$title|escape}}
    </h1>

    {{SnsWidget}}

    <div class="row">
      <div class="col-xs-12 col-sm-8 col-md-8 col-lg-9 table-responsive table-responsive-force">
        <table class="table table-condensed graph-container">
          <thead>
            <tr>
              <th></th>
              {{foreach $ruleNames as $ruleKey => $ruleName}}
                <th>
                  {{$ruleName|escape}}
                </th>
              {{/foreach}}
            </tr>
          </thead>
          <tbody>
            {{foreach $mapNames as $mapKey => $mapName}}
              <tr>
                <th>{{$mapName|escape}}</th>
                {{foreach $ruleNames as $ruleKey => $ruleName}}
                  <td>
                    <div class="pie-flot-container" data-json="{{$data[$mapKey][$ruleKey]|json_encode|escape}}">
                    </div>
                  </td>
                {{/foreach}}
              </tr>
            {{/foreach}}
          </tbody>
        </table>
      </div>
      <div class="col-xs-12 col-sm-4 col-md-4 col-lg-3">
        <div style="border:1px solid #ccc;border-radius:5px;padding:15px;margin-bottom:15px">
          {{$terms = [
            ''            => 'Any Time'|translate:'app',
            'this-period' => 'Current Period'|translate:'app',
            'last-period' => 'Previous Period'|translate:'app',
            '24h'         => 'Last 24 Hours'|translate:'app',
            'today'       => 'Today'|translate:'app',
            'yesterday'   => 'Yesterday'|translate:'app',
            'term'        => 'Specify Period'|translate:'app'
          ]}}
          {{use class="yii\bootstrap\ActiveForm" type="block"}}
          {{ActiveForm assign="_" id="filter-form" action=['show/user-stat-by-map-rule', 'screen_name' => $user->screen_name] method="get"}}
            {{$_->field($filter, 'lobby')->dropDownList($lobbies)->label(false)}}
            {{*$_->field($filter, 'rule')->dropDownList($rules)->label(false)*}}
            {{*$_->field($filter, 'map')->dropDownList($maps)->label(false)*}}
            {{$_->field($filter, 'weapon')->dropDownList($weapons)->label(false)}}
            {{*$_->field($filter, 'result')->dropDownList($results)->label(false)*}}
            {{$_->field($filter, 'term')->dropDownList($terms)->label(false)}}
            <div id="filter-term-group">
              {{$_->field($filter, 'term_from', [
                  'inputTemplate' => '<div class="input-group"><span class="input-group-addon">From:</span>{input}</div>'|translate:'app'
                ])->input('text', ['placeholder' => 'YYYY-MM-DD hh:mm:ss'])->label(false)}}
              {{$_->field($filter, 'term_to', [
                  'inputTemplate' => '<div class="input-group"><span class="input-group-addon">To:</span>{input}</div>'|translate:'app'
                ])->input('text', ['placeholder' => 'YYYY-MM-DD hh:mm:ss'])->label(false)}}

              {{\jp3cki\yii2\datetimepicker\BootstrapDateTimePickerAsset::register($this)|@void}}
              {{registerCss}}#filter-term-group{margin-left:5%}{{/registerCss}}
              {{registerJs}}
                (function($) {
                  $('#filter-term-group input').datetimepicker({
                    format: "YYYY-MM-DD HH:mm:ss"
                  });
                  $('#filter-term').change(function() {
                    if ($(this).val() === 'term') {
                      $('#filter-term-group').show();
                    } else {
                      $('#filter-term-group').hide();
                    }
                  }).change();
                })(jQuery);
              {{/registerJs}}
            </div>
            <input type="submit" value="{{'Summarize'|translate:'app'|escape}}" class="btn btn-primary">
          {{/ActiveForm}}
        </div>
        {{include file="@app/views/includes/user-miniinfo.tpl" user=$user}}
        {{AdWidget}}
      </div>
    </div>
  </div>
{{/strip}}
{{registerJs}}
window.statByMapRule();
(function($){
  var $th = $('.graph-container thead tr:nth-child(1) th');
  $th.css({
    width: '20%',
    'min-width': '150px'
  });
})(jQuery);
{{/registerJs}}
{{registerCss}}.pie-flot-container{height:200px}.pie-flot-container .error{display:none}{{/registerCss}}
