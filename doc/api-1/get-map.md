`GET /api/v1/map`
=================

URL: `https://stat.ink/api/v1/map`
Return-Type: `application/json`

マップ（ステージ）の一覧を JSON 形式、 [`map` 構造体](struct/map.md)の配列で返します。
各マップの `key` が他の API で利用するときの値です。

出現順に規定はありません。（利用者側で適切に並び替えてください）

```js
[
    {
        "key": "arowana",
        "name": {
            "en_US": "Arowana Mall",
            "ja_JP": "アロワナモール"
        }
    },
    {
        "key": "bbass",
        "name": {
            "en_US": "Blackbelly Skatepark",
            "ja_JP": "Bバスパーク"
        }
    },
    // ...
]
```
