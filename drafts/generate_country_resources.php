<?php
ini_set('intl.default_locale', 'tr-TR');
$localeBlackList = ['ar_001','en_001','en_150','en_US_POSIX','eo_001','es_419','ia_001','yi_001'];
echo PHP_EOL;
$list = [];
foreach (ResourceBundle::getLocales('') as $l) {
    if (in_array($l, $localeBlackList)) {
        continue;
    }
    $list[] = $l;
}
unset($l);
rsort($list);
$languages = [];
$countries = [];
foreach ($list as $locale1) {
    $v1 = null;
    $v2 = null;
    $v3 = null;
    [$v1, $v2, $v3] = explode('_', $locale1);
    if (null === $v2) {
        $languageCode = $v1;
        $countryCode = null;
        $alphabetCode = null;
    } elseif (null === $v3) {
        $languageCode = $v1;
        $countryCode = (2 === strlen($v2)) ? $v2 : null;
        $alphabetCode = null;
    } else {
        $languageCode = $v1;
        $countryCode = $v3;
        $alphabetCode = $v2;
    }
    if (null === $countryCode) {
        continue;
    }
    foreach ($list as $locale2) {
        //$languages[$languageCode]['alphabets'][$alphabetCode]['countries'][$countryCode] = $locale1;
        $countries[$countryCode]['component'] = 'country';
        $countries[$countryCode]['code'] = $countryCode;
        $country = trim(Locale::getDisplayRegion($locale1, $locale2));
        if (
            !isset($countries[$countryCode]['names'])
            || !is_array($countries[$countryCode]['names'])
        ) {
            $countries[$countryCode]['names'] = [];
        }
        if ($country !== $countryCode) {
            $key = array_search($country, array_reverse($countries[$countryCode]['names']));
            if (false !== $key) {
                if (strpos($key, '_') > 0) {
                    unset($countries[$countryCode]['names'][$key]);
                }
            }
            $countries[$countryCode]['names'][$locale2] = $country;
        }
        $countries[$countryCode]['languages'][$languageCode]['code'] = $languageCode;
        $language = Locale::getDisplayLanguage($locale1, $locale2);
        if (
            !isset($countries[$countryCode]['languages'][$languageCode]['names'])
            || !is_array($countries[$countryCode]['languages'][$languageCode]['names'])
        ) {
            $countries[$countryCode]['languages'][$languageCode]['names'] = [];
        }
        if ($language !== $languageCode) {
            $key = array_search($language, array_reverse($countries[$countryCode]['languages'][$languageCode]['names']));
            if (false !== $key) {
                if (strpos($key, '_') > 0) {
                    unset($countries[$countryCode]['languages'][$languageCode]['names'][$key]);
                }
            }
            $countries[$countryCode]['languages'][$languageCode]['names'][$locale2] = $language;
            if (null !== $alphabetCode) {
                $countries[$countryCode]['languages'][$languageCode]['alphabets'][$alphabetCode]['code'] = $alphabetCode;
                $alphabet = Locale::getDisplayScript($locale1, $locale2);
                if (
                    !isset($countries[$countryCode]['languages'][$languageCode]['alphabets'][$alphabetCode]['names'])
                    || !is_array($countries[$countryCode]['languages'][$languageCode]['alphabets'][$alphabetCode]['names'])
                ) {
                    $countries[$countryCode]['languages'][$languageCode]['alphabets'][$alphabetCode]['names'] = [];
                }
                if ($alphabet !== $alphabetCode) {
                    $key = array_search($alphabet, array_reverse($countries[$countryCode]['languages'][$languageCode]['alphabets'][$alphabetCode]['names']));
                    if (false !== $key) {
                        if (strpos($key, '_') > 0) {
                            unset($countries[$countryCode]['languages'][$languageCode]['alphabets'][$alphabetCode]['names'][$key]);
                        }
                    }
                    $countries[$countryCode]['languages'][$languageCode]['alphabets'][$alphabetCode]['names'][$locale2] = $alphabet;
                }
            }
        }
    }
}
foreach ($countries as $countryCode => &$country) {
    if (isset($country['languages'])) {
        $languageList = [];
        foreach ($country['languages'] as &$language) {
            if (isset($language['alphabets'])) {
                foreach ($language['alphabets'] as &$alphabet) {
                    ksort($alphabet['names']);
                }
            }
            ksort($language['names']);
            $languageList[] = $language;
        }
        $country['languages'] = $languageList;
    }
    ksort($country['names']);
    $directoryPath = 'cntrs/' . strtolower($countryCode);
    if (!file_exists($directoryPath)) {
        mkdir($directoryPath, 0755, true);
    }
    file_put_contents($directoryPath . '/country.json', json_encode($country, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
}
//print_r($countries);
echo PHP_EOL;
