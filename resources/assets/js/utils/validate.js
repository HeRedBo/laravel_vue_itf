
/**
 * 校验用户名称是否有效
 */
export function isvalidUsername(str) {
    var reg = /[~#^$@%&!?%*]/gi;
    if (reg.test(str.trim())) {
        return false;
    }
    return true;
}
