{{strip}}
  {{set layout="main.tpl"}}

  {{$name = '{0}-san'|translate:'app':$user->name}}
  {{$title = "{0}'s Battle Stat (Ranked Battle)"|translate:'app':$name}}
  {{set title="{{$app->name}} | {{$title}}"}}

  {{$this->registerMetaTag(['name' => 'twitter:card', 'content' => 'summary'])|@void}}
  {{$this->registerMetaTag(['name' => 'twitter:title', 'content' => $title])|@void}}
  {{$this->registerMetaTag(['name' => 'twitter:description', 'content' => $title])|@void}}
  {{$this->registerMetaTag(['name' => 'twitter:site', 'content' => '@stat_ink'])|@void}}
  {{if $user->twitter != ''}}
    {{$this->registerMetaTag(['name' => 'twitter:creator', 'content' => '@'|cat:$user->twitter])|@void}}
  {{/if}}

  {{\app\assets\FlotPieAsset::register($this)|@void}}
  <div class="container">
    <h1>
      {{$title|escape}}
    </h1>

    <div id="sns">
      {{\app\assets\TwitterWidgetAsset::register($this)|@void}}
      <a class="twitter-share-button" href="https://twitter.com/intent/tweet" data-count="none"><span class="fa fa-twitter"></span></a>
    </div>

    <div class="row">
      <div class="col-xs-12 col-sm-8 col-md-8 col-lg-9">
        <h2 id="exp">
          {{'Rank'|translate:'app'|escape}}
        </h2>
        <p>
          {{'Excluded: Private Battles and Squad Battles(when Rank S, S+)'|translate:'app'|escape}}
        </p>
        <script>
          window._rankData = {{$recentRank|json_encode}};
        </script>
        <div id="stat-rank-legend"></div>
        <div class="graph stat-rank"></div>
        <div class="graph stat-rank" data-limit="200"></div>
        <div class="text-right">
          <label>
            <input type="checkbox" id="show-rank-moving-avg" value="1" checked> {{'Show moving averages'|translate:'app'|escape}}
          </label>
        </div>

        <hr>

        <h2 id="wp">
          {{'Winning Percentage'|translate:'app'|escape}}
        </h2>
        <p>
          {{'Excluded: Private Battles'|translate:'app'|escape}}
        </p>
        <div class="graph" id="stat-wp" data-json="{{$recentWP|json_encode|escape}}">
        </div>
      </div>
      <div class="col-xs-12 col-sm-4 col-md-4 col-lg-3">
        {{include file="@app/views/includes/user-miniinfo.tpl" user=$user}}
        {{include file="@app/views/includes/ad.tpl"}}
    </div>
  </div>
{{/strip}}
{{registerCss}}
  .stat-rank{height:300px}
{{/registerCss}}
{{registerJs}}
(function($) {
  var $graphs = $('.graph');
  
  function drawRankGraph(json) {
    var $graph_ = $graphs.filter('.stat-rank');

    var rules = (function(json) {
      var ret = {
        area: [],
        yagura: [],
        hoko: []
      };
      var prevIndex = null;
      var prevRule = null;
      var prevValue = null;
      for (var i = 0; i < json.length; ++i) {
        var data = json[i];
        if (prevRule !== data.rule && prevRule !== null) {
          ret[prevRule].push([data.index, null]);
          ret[data.rule].push([prevIndex, prevValue]);
        }
        ret[data.rule].push([data.index, data.exp]);
        prevIndex = data.index;
        prevRule = data.rule;
        prevValue = data.exp;
      }
      return ret;
    })(json);

    var data = [
      {
        label: "{{'Rank'|translate:'app'|escape:'javascript'}} ({{'Splat Zones'|translate:'app-rule'|escape:'javascript'}})",
        data: rules.area,
        color: '#edc240'
      },
      {
        label: "{{'Rank'|translate:'app'|escape:'javascript'}} ({{'Tower Control'|translate:'app-rule'|escape:'javascript'}})",
        data: rules.yagura,
        color: '#40a2ed'
      },
      {
        label: "{{'Rank'|translate:'app'|escape:'javascript'}} ({{'Rainmaker'|translate:'app-rule'|escape:'javascript'}})",
        data: rules.hoko,
        color: '#ed4040'
      }
    ];

    if ($('#show-rank-moving-avg').prop('checked')) {
      data.push({
        label: "{{'Moving Avg. ({0} Battles)'|translate:'app':10|escape}}",
        data: json.map(function(v) {
          return [v.index, v.movingAvg];
        }),
        color: 'rgba(64,237,64,.5)'
      });
      data.push({
        label: "{{'Moving Avg. ({0} Battles)'|translate:'app':50|escape}}",
        data: json.map(function(v) {
          return [v.index, v.movingAvg50];
        }),
        color: 'rgba(148,64,237,.5)'
      });
    }

    $graph_.each(function() {
      var $graph = $(this);
      var limit = ~~$graph.attr('data-limit');
      if (limit > 0 && json.length <= limit) {
        $graph.hide();
        return;
      }

      $.plot($graph, data, {
        xaxis: {
          minTickSize: 1,
          min: limit > 0 ? -limit : null,
          tickFormatter: function (v) {
            return ~~v;
          }
        },
        yaxis: {
          minTickSize: 10,
          tickFormatter: function (v) {
            if (v >= 1100) {
              return v >= 1150 ? '' : 'S+ 99';
            }

            var rank = Math.floor(v / 100);
            var exp = v % 100;
            var ranks = ['C-', 'C', 'C+', 'B-', 'B', 'B+', 'A-', 'A', 'A+', 'S', 'S+'];
            return ranks[rank] + " " + exp;
          }
        },
        legend: {
          container: $('#stat-rank-legend')
        }
      });
    });
  }

  function drawWPGraph() {
    var $graph = $graphs.filter('#stat-wp');
    var json = JSON.parse($graph.attr('data-json'));
    var data = [
      {
        label: "{{'WP (Totally)'|translate:'app'|escape:'javascript'}}",
        data: json.map(
          function(v) {
            return [v.index, v.totalWP];
          },
          json
        )
      },
      {
        label: "{{'WP ({0} Battles)'|translate:'app':20|escape}}",
        data: json.map(
          function(v) {
            return [v.index, v.movingWP];
          },
          json
        )
      }
    ];
    $.plot($graph, data, {
      xaxis: {
        minTickSize: 1,
        tickFormatter: function (v) {
          return ~~v;
        }
      },
      yaxis: {
        min: 0,
        max: 100,
      },
      legend: {
        position: "nw"
      }
    });
  }

  var timerId = null;
  $(window).resize(function() {
    if (timerId !== null) {
      window.clearTimeout(timerId);
    }
    timerId = window.setTimeout(function() {
      $graphs.height($graphs.width() * 9 / 16);
      drawRankGraph(window._rankData);
      drawWPGraph();
    }, 33);
  }).resize();

  $('#show-rank-moving-avg').click(function () {
    $(window).resize();
  });
})(jQuery);
{{/registerJs}}