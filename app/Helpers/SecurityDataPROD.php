<?php

namespace App\Helpers;

class SecurityDataPROD
{
	//** Merchant ID / OFFICEID: 9103332177 for testing
    /**
     * JWE Key Id.
     *
     * @var string
     */
    public static string $EncryptionKeyId = "19f84b5655f04e25a99b09f1ee2fac78";

    /**
     * Access Token.
     *
     * @var string
     */
    public static string $AccessToken = "94e9a42359b211edaf360279bcee2f04"; //USD

    /**
     * Token Type - Used in JWS and JWE header.
     *
     * @var string
     */
    public static string $TokenType = "JWT";

    /**
     * JWS (JSON Web Signature) Signature Algorithm - This parameter identifies the cryptographic algorithm used to
     * secure the JWS.
     *
     * @var string
     */
    public static string $JWSAlgorithm = "PS256";

    /**
     * JWE (JSON Web Encryption) Key Encryption Algorithm - This parameter identifies the cryptographic algorithm
     * used to secure the JWE.
     *
     * @var string
     */
    public static string $JWEAlgorithm = "RSA-OAEP";

    /**
     * JWE (JSON Web Encryption) Content Encryption Algorithm - This parameter identifies the content encryption
     * algorithm used on the plaintext to produce the encrypted ciphertext.
     *
     * @var string
     */
    public static string $JWEEncrptionAlgorithm = "A128CBC-HS256";

    /**
     * Merchant Signing Private Key is used to cryptographically sign and create the request JWS.
     *
     * @var string
     */
    public static string $MerchantSigningPrivateKey = "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";
    
    /*
     * PACO Encryption Public Key is used to cryptographically encrypt and create the request JWE.
     *
     * @var string
     */
    public static string $PacoEncryptionPublicKey ="MIICIjANBgkqhkiG9w0BAQEFAAOCAg8AMIICCgKCAgEA6ZLups2K0iYEMxQqgASX8gY6tWhNVCp08YuDgjCsOVrGVgUHD0dh0TWFNJ7Lq2Jp0SOsGgi54+hrjwPOL2CCZxw8pKUlL57UksoD9oWUrK/KkSvEAwPU4cZqzxIXyhBcZb8O96iN4WQJILkRTg+DXLkML6qisO496fPGIs+vCoc87toucy5O9fRfaYSjcqjreyi8JDkvVJM/BeNtOEM2a0b/lcWa67RH+tN97H25k+Qez7QthLru6oBfWBgD6iIwhV+ICqLWHmp6fQ+DHQk/o+OO3yFiY9OAvMiy8MOTinvkBlFwYgYNznG3/w0Xh8U5vtudUXPDNUO6ddf4y99+6LlWDiKgJn/Th93YUg+gFH4LUJHyPrSY2JuC+Q8kksp2xyiZDTHGzi96kturwrqCui6TytCHcU4UB0VRMR+M7VRl3S2YPhcxv5U8Fh2PITqydZE5vv1Va06qhegjOlSZnEUl2xKPm5k/u+UHvUP/oq04fQLTlYqyA3JYDCe4z5Ea2SOgjeVl+qTatWYzmkUXyCONLZ4UaRrgbYCp0nCPHoTFgRQdChu8ezDbnYY9IW7cT/s2fEi5N7X1XrQttiEP4rbn0y0qVYYjN86+elfhtYGHidZTUSUS5RSTHqOkj59p5LIGwFF9iTXzCjfUqq8clnfOk76qSLY1+Kj+SMMe6Z8CAwEAAQ==";
    
    /*
     * PACO Signing Public Key is used to cryptographically verify the response JWS signature.
     *
     * @var string
     */
    public static string $PacoSigningPublicKey = "MIICIjANBgkqhkiG9w0BAQEFAAOCAg8AMIICCgKCAgEAr0XW6QacR8GilY4nZrJZW40wnFeYu7h9aXUSqxCP6djurCWZmLqnrsYWP7/HR8WOulYPHTVpfqJesTOdVqPgY6p10H811oRbJG9jvsG8j8kn/Bk8b2wZ9qelNqdNJMDbR5WUyaytaDWW6QdI4+clqjFfwCOw76noDSe+R4pDSzgMiyCk5R4m2ECT1fv/4Axz2bvLN+DRTg5DPPIMLWpA87lgjxeaDlGyJqZCbkJozW7JX0AJVc0X7YR9kzbiTi3LVOInSKY+VHT8yCARIdvXtKc6+IWSbVQqgpNIBB8GN0OvU8xedjPNCMGZnnMtgd7XLTf/okyadbdNLAqQLTbDs/5HnIVx8FyfgiOS/zsim5ivi3ljVAW3T3ePGjkY0q1DMzr5iJ4m/WTL2d1TArlfHyQhkSpFpQPOO+pJyVQqttHJo99vMirQogdSx4lIu//aod0yJyJLpjCeiqb2Fz3Qk0AZ4S78QKeeGsxTRchTP6Wsb6okaZd+cFi6z8qbP0z/Y3xRZO7vOLB/whkqS+pMVKBQ42YzgQPRzbXXmgCkf1nCqgrD9bnIB5ovdRGfDXW86GKY8XwGVjb4BoMvql+HsbonKHAO+eGfQulpB5YfQGQU3ZXdMdfCLAk8FuqemH4k7S7diLzVvRCuisHsEx6qJ4ewxzNCvW7OGVinTR9NSQUCAwEAAQ==";
    
    /**
     * Merchant Decryption Private Key used to cryptographically decrypt the response JWE.
     * @var string
     */
    public static string $MerchantDecryptionPrivateKey = "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";

}