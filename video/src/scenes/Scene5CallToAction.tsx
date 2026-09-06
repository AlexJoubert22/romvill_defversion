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

export const Scene5CallToAction: React.FC = () => {
  const frame = useCurrentFrame();

  const entrance = interpolate(frame, [10, 40], [0, 1], {
    easing: Easing.bezier(0.16, 1, 0.3, 1),
    extrapolateLeft: 'clamp',
    extrapolateRight: 'clamp',
  });

  const buttonPulse = interpolate(
    Math.sin(frame / 8),
    [-1, 1],
    [1, 1.04]
  );

  const buttonGlow = interpolate(
    Math.sin(frame / 8),
    [-1, 1],
    ['0 0 30px rgba(191, 161, 95, 0.4)', '0 0 60px rgba(191, 161, 95, 0.8)']
  );

  const bgScale = interpolate(frame, [0, 330], [1.0, 1.08], {
    extrapolateRight: 'clamp',
  });

  return (
    <AbsoluteFill style={{ backgroundColor: '#06090F' }}>
      {/* Voiceover Track */}
      <Audio src={staticFile('audio/scene5.mp3')} volume={1} />

      {/* Cinematic Mediterranean coastal background */}
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
            filter: 'brightness(0.25) contrast(1.25) saturate(0.8)',
          }}
        />
        <div
          style={{
            position: 'absolute',
            inset: 0,
            background:
              'radial-gradient(circle at center, rgba(6, 9, 15, 0.5) 0%, rgba(6, 9, 15, 0.96) 100%)',
          }}
        />
      </div>

      <PrecisionGrid
        locationLabel="COMMISSION INTELLIGENCE BRIEF"
        coordinates="SECURE PROTOCOL ACTIVE"
        statusLabel="NOW ACCEPTING CLIENTS"
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
          textAlign: 'center',
          padding: '0 60px',
        }}
      >
        {/* Brand Header */}
        <div
          style={{
            opacity: entrance,
            transform: `translateY(${interpolate(entrance, [0, 1], [30, 0])}px)`,
            display: 'flex',
            flexDirection: 'column',
            alignItems: 'center',
            marginBottom: 24,
          }}
        >
          <Img
            src={staticFile('images/rv-logo-white.png')}
            style={{
              height: 52,
              objectFit: 'contain',
              marginBottom: 16,
              filter: 'drop-shadow(0 0 20px rgba(191, 161, 95, 0.4))',
            }}
          />
          <div
            style={{
              fontSize: 16,
              letterSpacing: '0.28em',
              fontWeight: 700,
              color: '#BFA15F',
              textTransform: 'uppercase',
            }}
          >
            CRITERIO ANTES DE DECIDIR · CLARITY BEFORE DECISION
          </div>
        </div>

        {/* Main CTA Heading */}
        <h1
          style={{
            opacity: entrance,
            transform: `translateY(${interpolate(entrance, [0, 1], [25, 0])}px)`,
            fontSize: 60,
            fontWeight: 800,
            color: '#F2F5FA',
            letterSpacing: '-0.03em',
            lineHeight: 1.15,
            maxWidth: 960,
            margin: '0 0 32px 0',
          }}
        >
          Don't buy blind. Decide with complete institutional certainty.
        </h1>

        {/* Primary Interactive Gold Button */}
        <div
          style={{
            opacity: entrance,
            transform: `scale(${buttonPulse})`,
            boxShadow: buttonGlow,
            display: 'inline-flex',
            alignItems: 'center',
            gap: 16,
            padding: '20px 48px',
            backgroundColor: '#BFA15F',
            borderRadius: 8,
            color: '#0A0F18',
            fontWeight: 800,
            fontSize: 22,
            letterSpacing: '0.08em',
            marginBottom: 28,
            cursor: 'pointer',
          }}
        >
          <span>VISIT ROMVILL.COM</span>
          <span style={{ fontSize: 24, fontWeight: 900 }}>→</span>
        </div>

        {/* Sub-contact details */}
        <div
          style={{
            opacity: entrance,
            display: 'flex',
            alignItems: 'center',
            gap: 24,
            fontSize: 15,
            color: '#A9B4C6',
            letterSpacing: '0.12em',
            textTransform: 'uppercase',
          }}
        >
          <span>DIRECT INQUIRIES: <strong style={{ color: '#F2F5FA' }}>contacto@romvill.com</strong></span>
          <span style={{ color: 'rgba(255,255,255,0.2)' }}>•</span>
          <span>WHATSAPP DIRECT CONCIERGE</span>
          <span style={{ color: 'rgba(255,255,255,0.2)' }}>•</span>
          <span style={{ color: '#BFA15F' }}>ALICANTE · MÁLAGA · MARBELLA</span>
        </div>
      </div>
    </AbsoluteFill>
  );
};
