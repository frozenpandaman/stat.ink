`special` 構造体
==================

`special` 構造体はある特定のスペシャルウェポンを指し、次のような構造となっています。

```js
{
    "key": "barrier",
    "name": {
        "en_US": "Bubbler",
        "ja_JP": "バリア"
    }
}
```

* `key` : 識別する時に使用するキーです。たとえば `GET /api/v1/weapon` API で絞り込む際に指定します。

* `name` : 名前を [`name` 構造体](name.md) で表します。
