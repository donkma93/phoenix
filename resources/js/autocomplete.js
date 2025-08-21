export default function (inp, arr, id) {
  let i = 0,
  len = arr.length,
  dl = document.createElement('datalist');

  dl.id = id;
  for (; i < len; i += 1) {
      var option = document.createElement('option');
      option.value = arr[i];
      dl.appendChild(option);
  }
  inp.appendChild(dl);
}