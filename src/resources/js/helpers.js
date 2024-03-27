export const isImage = (attachment) => {
    let mime = attachment.mime || attachment.type
    mime = mime.split('/');
    return mime[0].toLowerCase() === 'image';
}

export const isVideo = (attachment) => {
    let mime = attachment.mime || attachment.type
    mime = mime.split('/');
    return mime[0].toLowerCase() === 'video';
}

export const wordEndingsParser = (count, word, end1, end2, end3) => {
    let end;
    if (count % 10 === 1 && count % 100 !== 11) {
        end = end1;
    } else if ([2, 3, 4].includes(count % 10) && ![12, 13, 14].includes(count % 100)) {
        end = end2;
    } else {
        end = end3;
    }
    const formatCount = count.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ',');
    return formatCount + ' ' + word + end;
}

export const matchHref = (body) => {
    const urlRegex = /<a.+href="((https?):\/\/[^"]+)"/;
    const match = body.match(urlRegex);

    if (match && match.length > 0) {
        return (match[1]);
    }
    return null
}

export const matchLink = (body) => {
    const urlRegex = /(https?):\/\/[^\s<]+/;
    const match = body.match(urlRegex);

    if (match && match.length > 0) {
        return (match[0]);
    }
    return null
}
