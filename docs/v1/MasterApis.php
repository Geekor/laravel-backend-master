<?php

/**
 * 这个文件专门用于生成后台 API 文档用的
 */
class MasterApis
{
    /**
     * 标准头信息
     * 
     * @apiDefine CommonMasterHeader
     *
     * @apiHeader {String} Accept = application/json 必须添加的头信息
     * @apiHeader {String} App-Mark 应用标识（表明自己是哪个 APP，例如: pet / hpy / hiv / cvd / scd / fsr /...）
     * @apiHeader {String} App-Version 应用版本号 （表明自己是哪个 版本，例如: 1.0.0）
     * @apiHeader {String} User-Locale 用户语言（表明用户是用哪个语言，例如: en, zh_cn, zh_tw）
     * 
     * @apiParam {String} prefix=backend 管理员后台 API 的前缀，根据项目配置会有所不同
     *
     */
    /// ======================================================================================================


     /**
     * @api {post} /api/:prefix/auth/login A 登录后台 [POST]
     * @apiVersion 1.0.0
     * @apiPermission none
     * @apiName Login
     * @apiGroup MasterAuth
     * @apiDescription 管理员登录
     *
     * @apiUse CommonMasterHeader
     *
     * @apiBody {String} username 账号
     * @apiBody {String} password 密码
     * @apiBody {String} device_name 登录设备名
     *
     * @apiSuccessExample {json} Success-Response
     * HTTP/1.1 200 OK
     * {
     *     "info": {
     *         "id": 1,
     *         "username": "admin",
     *         "name": "超级管理员",
     *         "created_at": "2022-05-16T12:50:55.000000Z",
     *         "updated_at": "2022-05-16T12:50:55.000000Z"
     *     },
     *     "token": "1|tHzLfwppJbUWvjIyb1T5b6ZWw1XBFyThtQc4Rlcs"
     * }
     *
     * @apiError (ERROR CODE)  4000 登录失败 （反正就是失败了，目前还没时间细分）
     * @apiErrorExample {json} 登录失败
     * HTTP/1.1 400 Bad Request
     * {
     *     "code": 4000,
     *     "message": "SEE_DETAIL",
     *     "detail": "缺少参数"
     * }
     */
    public function login() {}

    /**
     * @api {delete} /api/:prefix/auth/me C 登出 [DEL]
     * @apiVersion 1.0.0
     * @apiPermission sanctum
     * @apiName Logout
     * @apiGroup MasterAuth
     * @apiDescription 注销 token。
     *
     * @apiUse CommonMasterHeader
     * @apiHeader {String} Authorization 用户的认证 token, value 以 Bearer 开头
     * @apiBody {String} [_method]  "DELETE"（对于不支持 delete 方法的客户端，可以采用 post 方法，然后加入本参数）
     *
     * @apiSuccessExample {json} Success-Response:
     * HTTP/1.1 204 No Content
     *
     */
    public function logout() {}
}