export default function terbilang(a) {
  var bilangan = [
    "",
    "Satu",
    "Dua",
    "Tiga",
    "Empat",
    "Lima",
    "Enam",
    "Tujuh",
    "Delapan",
    "Sembilan",
    "Sepuluh",
    "Sebelas"
  ];

  // 1 - 11
  if (a < 12) {
    var kalimat = bilangan[a];
  }
  // 12 - 19
  else if (a < 20) {
    var kalimat = bilangan[a - 10] + "Belas";
  }
  // 20 - 99
  else if (a < 100) {
    var utama = a / 10;
    var depan = parseInt(String(utama).substr(0, 1));
    var belakang = a % 10;
    var kalimat = bilangan[depan] + "Puluh " + bilangan[belakang];
  }
  // 100 - 199
  else if (a < 200) {
    var kalimat = "Seratus " + terbilang(a - 100);
  }
  // 200 - 299
  else if (a < 300) {
    var kalimat = "Duaratus " + terbilang(a - 200);
  }
  // 300 - 399
  else if (a < 400) {
    var kalimat = "Tigaratus " + terbilang(a - 300);
  }
  // 400 - 499
  else if (a < 500) {
    var kalimat = "Empatratus " + terbilang(a - 400);
  }
  // 500 - 599
  else if (a < 600) {
    var kalimat = "Limaratus " + terbilang(a - 500);
  }
  // 600 - 699
  else if (a < 700) {
    var kalimat = "Enamratus " + terbilang(a - 600);
  }
  // 700 - 799
  else if (a < 800) {
    var kalimat = "Tujuhratus " + terbilang(a - 700);
  }
  // 800 - 899
  else if (a < 900) {
    var kalimat = "Delapanratus " + terbilang(a - 800);
  }
  // 900 - 999
  else if (a < 1000) {
    var kalimat = "Sembilanratus " + terbilang(a - 900);
  }
  // 1,000 - 1,999
  else if (a < 2000) {
    var kalimat = "Seribu " + terbilang(a - 1000);
  }
  // 2,000 - 9,999
  else if (a < 10000) {
    var utama = a / 1000;
    var depan = parseInt(String(utama).substr(0, 1));
    var belakang = a % 1000;
    var kalimat = bilangan[depan] + " Ribu " + terbilang(belakang);
  }
  // 10,000 - 99,999
  else if (a < 100000) {
    var utama = a / 100;
    var depan = parseInt(String(utama).substr(0, 2));
    var belakang = a % 1000;
    var kalimat = terbilang(depan) + " Ribu " + terbilang(belakang);
  }
  // 100,000 - 999,999
  else if (a < 1000000) {
    var utama = a / 1000;
    var depan = parseInt(String(utama).substr(0, 3));
    var belakang = a % 1000;
    var kalimat = terbilang(depan) + " Ribu " + terbilang(belakang);
  }
  // 1,000,000 - 	99,999,999
  else if (a < 100000000) {
    var utama = a / 1000000;
    var depan = parseInt(String(utama).substr(0, 4));
    var belakang = a % 1000000;
    var kalimat = terbilang(depan) + " Juta " + terbilang(belakang);
  } else if (a < 1000000000) {
    var utama = a / 1000000;
    var depan = parseInt(String(utama).substr(0, 4));
    var belakang = a % 1000000;
    var kalimat = terbilang(depan) + " Juta " + terbilang(belakang);
  } else if (a < 10000000000) {
    var utama = a / 1000000000;
    var depan = parseInt(String(utama).substr(0, 1));
    var belakang = a % 1000000000;
    var kalimat = terbilang(depan) + " Milyar " + terbilang(belakang);
  } else if (a < 100000000000) {
    var utama = a / 1000000000;
    var depan = parseInt(String(utama).substr(0, 2));
    var belakang = a % 1000000000;
    var kalimat = terbilang(depan) + " Milyar " + terbilang(belakang);
  } else if (a < 1000000000000) {
    var utama = a / 1000000000;
    var depan = parseInt(String(utama).substr(0, 3));
    var belakang = a % 1000000000;
    var kalimat = terbilang(depan) + " Milyar " + terbilang(belakang);
  } else if (a < 10000000000000) {
    var utama = a / 10000000000;
    var depan = parseInt(String(utama).substr(0, 1));
    var belakang = a % 10000000000;
    var kalimat = terbilang(depan) + " Triliun " + terbilang(belakang);
  } else if (a < 100000000000000) {
    var utama = a / 1000000000000;
    var depan = parseInt(String(utama).substr(0, 2));
    var belakang = a % 1000000000000;
    var kalimat = terbilang(depan) + " Triliun " + terbilang(belakang);
  } else if (a < 1000000000000000) {
    var utama = a / 1000000000000;
    var depan = parseInt(String(utama).substr(0, 3));
    var belakang = a % 1000000000000;
    var kalimat = terbilang(depan) + " Triliun " + terbilang(belakang);
  } else if (a < 10000000000000000) {
    var utama = a / 1000000000000000;
    var depan = parseInt(String(utama).substr(0, 1));
    var belakang = a % 1000000000000000;
    var kalimat = terbilang(depan) + " Kuadriliun " + terbilang(belakang);
  }

  console.log(kalimat)
  var full = [];
  if (kalimat) {
    var pisah = kalimat.replace(",", " ").split(" ");
    console.log(pisah)
    for (var i = 0; i < pisah.length; i++) {
      if (pisah[i] != "") {
        full.push(pisah[i].toLowerCase());
      }
    }
  }
  return full;
}
