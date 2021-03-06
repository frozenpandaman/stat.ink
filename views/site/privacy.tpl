{{strip}}
  {{set layout="main.tpl"}}
  {{set title="{{$app->name}} | {{'Privacy Policy'|translate:'app'}}"}}
  <div class="container">
    <h1>
      {{'Privacy Policy'|translate:'app'}}
    </h1>

    {{AdWidget}}
    {{SnsWidget}}

    <p>弊サイト（{{$app->name|escape}}）では次の情報をアクセスログとして収集・蓄積しています。</p>
    <ul>
      <li>
        アクセス日時
      </li>
      <li>
        IPアドレス
      </li>
      <li>
        訪問元サイトURLに関する自己申告情報（<code>Referrer</code>）
      </li>
      <li>
        訪問者の利用しているブラウザやOSに関する自己申告情報（<code>User-Agent</code>）
      </li>
    </ul>
    <p>
      弊サイトでは Cookie を利用しています。
    </p>
    <p>
      弊サイトでは Google Analytics を利用してアクセス解析を行っています。収集内容等については Google Analytics の仕様に準じます。
    </p>
    <p>
      弊サイトで収集した情報のうち、個人の特定が可能であるなどセンシティブな情報（例えばIPアドレス）に関しては一切の公開等行いません。
      その他の情報、例えば一般的なアクセス元やOS、ブラウザの種類などは統計情報として取り扱います。
    </p>
    <p>
      ただし、警察等の捜査機関からの正式な依頼があった場合、または、弊サイトへのいわゆるサイバー攻撃等があり必要があった場合には、
      上の規定に関わらず然るべき機関への情報開示を行います。
    </p>

    <h2 id="image">
      {{'Image Sharing'|translate:'app'|escape}}
    </h2>
    <p>
      {{$app->name|escape}} に投稿されたデータや画像およびそれらの修正履歴は、 IkaLog の画像認識精度向上のために IkaLog の開発者（協力者を含む）と共有されます。
    </p>
    <p>
      共有は自動的に行われ、バトル削除を行っても削除されません。
    </p>
    <p>
      この供給は、2015-10-27 (JST) に開始されました。
    </p>
  </div>
{{/strip}}
