export const calculateRotation = (minRotation, maxRotation, ...params) => {

    if (params.length === 0) {
        throw new Error("At least one parameter is required.");
    }

    const uniqueKey = params.join(',');
    const hash = hashString(uniqueKey);

    const u1 = randomFromHash(hash);
    const u2 = randomFromHash(hash * 33); // Slightly different seed

    const standardNormal = boxMullerTransform(u1, u2);

    const center = (minRotation + maxRotation) / 2;
    const halfRange = (maxRotation - minRotation) / 2;

    return Math.max(minRotation, Math.min(maxRotation, center + standardNormal * (halfRange / 3)));
}

function hashString(str) {
    let hash = 5381;
    for (let i = 0; i < str.length; i++) {
        hash = (hash * 33) ^ str.charCodeAt(i);
    }
    return hash >>> 0;
}

function randomFromHash(hash) {
    return (hash % 1e6) / 1e6;
}

function boxMullerTransform(u1, u2) {
    return Math.sqrt(-2 * Math.log(u1)) * Math.cos(2 * Math.PI * u2);
}
