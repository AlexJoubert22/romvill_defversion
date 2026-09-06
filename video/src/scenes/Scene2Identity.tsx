import React from 'react';
import {
  AbsoluteFill,
  Easing,
  Img,
  interpolate,
  staticFile,
  useCurrentFrame,
} from 'remotion';
import { Audio } from '@remotion/media';
import { PrecisionGrid } from '../components/PrecisionGrid';
import { MetricCounter } from '../components/MetricCounter';

export const Scene2Identity: React.FC = () => {
  const frame = useCurrentFrame();

  // Logo Reveal Animation
  const logoScale = interpolate(frame, [10, 45], [0.8, 1], {
    easing: Easing.bezier(0.16, 1, 0.3, 1),
    extrapolateLeft: 'clamp',
    extrapolateRight: 'clamp',
  });
  const logoOpacity = interpolate(frame, [10, 35], [0, 1], {
    extrapolateLeft: 'clamp',
    extrapolateRight: 'clamp',
  });

  // Statement animation
  const statementOpacity = interpolate(frame, [45, 70], [0, 1], {
    extrapolateLeft: 'clamp',
    extrapolateRight: 'clamp',
  });
  const statementY = interpolate(frame, [45, 70], [25, 0], {
    easing: Easing.bezier(0.16, 1, 0.3, 1),
    extrapolateLeft: 'clamp',
    extrapolateRight: 'clamp',
  });

  // Background subtle pan
  const bgScale = interpolate(frame, [0, 520], [1.05, 1.15], {
    extrapolateRight: 'clamp',
  });

  return (
    <AbsoluteFill style={{ backgroundColor: '#070C15' }}>
      {/* Voiceover Track */}
      <Audio src={staticFile('audio/scene2.mp3')} volume={1} />

      {/* Background with Atmospheric Blueprint Grid */}
      <div
        style={{
          position: 'absolute',
          inset: 0,
          overflow: 'hidden',
        }}
      >
        <Img
          src={staticFile('images/fondo_hero.webp')}
          style={{
            width: '100%',
            height: '100%',
            objectFit: 'cover',
            transform: `scale(${bgScale})`,
            filter: 'brightness(0.24) contrast(1.2) saturate(0.8)',
          }}
        />
        <div
          style={{
            position: 'absolute',
            inset: 0,
            background:
              'radial-gradient(circle at center, rgba(7, 12, 21, 0.6) 0%, rgba(7, 12, 21, 0.96) 100%)',
          }}
        />
      </div>

      <PrecisionGrid
        locationLabel="TERRITORIAL INTELLIGENCE HQ"
        coordinates="ALICANTE · MÁLAGA · MARBELLA"
        statusLabel="NEUTRAL AUDITOR"
      />

      <div
        style={{
          position: 'absolute',
          inset: 0,
          display: 'flex',
          flexDirection: 'column',
          alignItems: 'center',
          justifyContent: 'center',
          zIndex: 10,
          padding: '0 60px',
        }}
      >
        {/* Brand Shield & Emblem */}
        <div
          style={{
            opacity: logoOpacity,
            transform: `scale(${logoScale})`,
            display: 'flex',
            flexDirection: 'column',
            alignItems: 'center',
            marginBottom: 28,
          }}
        >
          <div
            style={{
              padding: '16px 28px',
              borderRadius: 12,
              backgroundColor: 'rgba(16, 22, 34, 0.8)',
              border: '1px solid rgba(191, 161, 95, 0.4)',
              boxShadow: '0 0 40px rgba(191, 161, 95, 0.25)',
              display: 'flex',
              alignItems: 'center',
              gap: 20,
              marginBottom: 16,
            }}
          >
            <Img
              src={staticFile('images/rv-logo-white.png')}
              style={{
                height: 44,
                objectFit: 'contain',
              }}
            />
            <div style={{ width: 1, height: 32, backgroundColor: 'rgba(191, 161, 95, 0.4)' }} />
            <div style={{ textAlign: 'left' }}>
              <div
                style={{
                  fontSize: 24,
                  fontWeight: 800,
                  letterSpacing: '0.24em',
                  color: '#F2F5FA',
                }}
              >
                ROMVILL
              </div>
              <div
                style={{
                  fontSize: 11,
                  letterSpacing: '0.28em',
                  color: '#BFA15F',
                  fontWeight: 700,
                  textTransform: 'uppercase',
                }}
              >
                TERRITORIAL INTELLIGENCE
              </div>
            </div>
          </div>

          <div
            style={{
              fontSize: 14,
              letterSpacing: '0.2em',
              color: '#A9B4C6',
              fontWeight: 600,
              textTransform: 'uppercase',
            }}
          >
            NOT A REAL ESTATE AGENCY · WE ARE INDEPENDENT ANALYSTS
          </div>
        </div>

        {/* The Core Value Proposition */}
        <div
          style={{
            opacity: statementOpacity,
            transform: `translateY(${statementY}px)`,
            textAlign: 'center',
            maxWidth: 1000,
            marginBottom: 44,
          }}
        >
          <h2
            style={{
              fontSize: 48,
              fontWeight: 800,
              color: '#F2F5FA',
              letterSpacing: '-0.025em',
              lineHeight: 1.2,
              margin: '0 0 14px 0',
            }}
          >
            Zero Commission Bias. Zero Developer Interests.
          </h2>
          <p
            style={{
              fontSize: 20,
              color: '#A9B4C6',
              lineHeight: 1.5,
              margin: 0,
            }}
          >
            We provide deep forensic due diligence on the exact location where you plan to{' '}
            <span style={{ color: '#EFE3C6', fontWeight: 600 }}>buy, invest, or relocate</span>.
          </p>
        </div>

        {/* 3 Institutional Trust Counters */}
        <div
          style={{
            display: 'flex',
            gap: 28,
            justifyContent: 'center',
            width: '100%',
            maxWidth: 1080,
          }}
        >
          <MetricCounter
            targetNumber={50}
            prefix="+"
            title="Zones Analyzed"
            subtitle="Micro-territorial audits across the Mediterranean coast"
            delay={85}
          />
          <MetricCounter
            targetNumber={40}
            prefix="+"
            title="Verified Sources"
            subtitle="Police registries, cadastre, official urban masterplans"
            delay={110}
          />
          <MetricCounter
            targetNumber={100}
            suffix="%"
            title="Pure Independent"
            subtitle="No real estate ties. 100% on the buyer's side."
            delay={135}
          />
        </div>
      </div>
    </AbsoluteFill>
  );
};
