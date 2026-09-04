# duipai.top 上线包

这个目录已经可以直接作为静态网站根目录部署。

## 域名审核期间

可以先用 GitHub Pages / CloudBase 默认域名预览。所有资源均为相对路径，不需要改代码。

## GitHub Pages + duipai.top（免费）

1. 新建一个公开 GitHub 仓库，例如 `duipai-site`。
2. 将本目录中的全部文件上传到仓库根目录。
3. Settings → Pages → Deploy from a branch → `main` / root。
4. Pages 的 Custom domain 填 `duipai.top`。本目录已带 `CNAME`。
5. 到域名 DNS 控制台按 GitHub Pages 页面给出的记录完成解析。
6. DNS 生效后开启 Enforce HTTPS。

## CloudBase + duipai.top

1. 将本目录作为静态网站上传到 CloudBase 静态托管。
2. 先用平台默认域名验证页面和资源。
3. 域名实名/备案等条件满足后，在静态托管中绑定 `duipai.top` 并按控制台要求配置 DNS。

## 正式推广前只剩两项必须替换

- 首页 CTA 右侧“微信小程序体验码待替换” → 换成真实体验码/正式小程序码。
- 如果有真机截图，建议新增或替换当前 `assets/` 下的演示摄影素材。

## 当前站点主线

- 定位：让想一起玩音乐的人更快找到彼此。
- 核心：对拍找人/找乐队、个人音乐主页、私聊/群聊、乐队组织、活动公告、排练与设备协同。
- 双边价值：乐手减少组队摩擦；社团减少重复通知与人工统计。
